<?php


namespace APIServices\Zendesk_Linkedin\MessagesBuilder;

use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\IMessageTransform;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\App;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\CommentTransform;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\ImageTransform;

/**
 * Class MessageBuilder
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
abstract class MessageBuilder implements IMessageTransform
{
    /**
     * @var $commentType
     */
    protected $commentType;
    /**
     * @var $imageType
     */
    protected $imageType;

    /**
     * @var $messageImage
     */
    protected $messageImage = [];

    /**
     * @var $messageComment
     */
    protected $messageComment = [];

    /**
     * @var array $ListPosts
     */
    protected $ListPosts;
    /**
     * @var $linkedinService
     */
    protected $linkedinService;
    /**
     * @var $metadata
     */
    protected $metadata;

    /**
     * MessageBuilder constructor.
     * @param $metadata
     * @param LinkedinService $linkedinService
     * @throws \Exception
     */
    public function __construct($metadata, LinkedinService $linkedinService)
    {
        $this->linkedinService = $linkedinService;
        $this->metadata = $metadata;
        $this->ListPosts = $this->linkedinService->getUpdates($metadata);

    }

    /**
     * tracking and return an array already transformed all the Comments,
     * Images and Videos into a zendesk format
     * @param $messages
     * @return array
     * @throws \Throwable
     */
    function getFactoryMessage($messages)
    {
        try {
            foreach ($messages as $message) {
                $newArray = $this->linkedinService->getAllCommentPost($message, $this->metadata['access_token']);
                if (array_key_exists('content', $newArray['updateContent']['companyStatusUpdate']['share'])) {
                    $this->imageType = App::make(ImageTransform::class);
                    $this->messageImage = array_merge($this->imageType->getTransformedMessage($newArray), $this->messageImage);
                } else
                    if (array_key_exists('comment', $newArray['updateContent']['companyStatusUpdate']['share'])) {
                        $this->commentType = App::make(CommentTransform::class);
                        $this->messageComment = array_merge($this->messageComment, $this->commentType->getTransformedMessage($newArray));
                    } else {
                        Log::debug("video goes here");
                    }
            }
            $response = array_merge($this->messageComment, $this->messageImage);
            return $response;
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'redirect a messageType');
            throw $exception;
        }
    }

    /**
     * This method tracking the previous arrays to convert it
     *  into a zendesk array format
     * @param $newMessages
     * @return array
     */
    public function trackingMessage($newMessages)
    {
        $loopHep = [];
        $indexNewMessage = [];
        $messageLoopTransformed = [];
        foreach ($newMessages as $key => $indexNewMessage) {
            $messageLoopTransformed[] = $indexNewMessage;
            $indexNewMessage = array_merge($messageLoopTransformed, $loopHep);
        }
        return $indexNewMessage;
    }

    /**
     * sort the array to replace and arrange the array as the first element a post
     * @param $messagesTransformed
     * @return array
     */
    public function sortMessages($messagesTransformed)
    {
        return collect($messagesTransformed)->sortByDesc('created_at')->reverse()->toArray();
    }

    /**
     * @param $message
     * @return string
     */
    public function transformLinkedInTimestamp($message)
    {
        return gmdate('Y-m-d\TH:i:s\Z', $message / 1000);

    }

}