<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder;


use APIServices\LinkedIn\Services\LinkedinService;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\CommentTransform;
use APIServices\Zendesk_Linkedin\Models\MessageTypes\ImageTransform;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

/**
 * Class TransformMessageBuilder
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder
 */
class TransformMessageBuilder
{
    /**
     * @var $linkedinService
     */
    protected $linkedinService;
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
     * @var $metadata
     */
    protected $metadata;


    /**
     * TransformMessageBuilder constructor.
     * @param $metadata
     * @param LinkedinService $linkedinService
     */
    public function __construct($metadata, LinkedinService $linkedinService)
    {
        $this->linkedinService = $linkedinService;
        $this->metadata = $metadata;
    }
    /**
     * tracking and return an array already transformed all the Comments,
     * Images and Videos into a zendesk format
     * @param $messages
     * @return array
     * @throws \Throwable
     */
    function getTransformedMessage($messages)
    {
        try {
            foreach ($messages as $message) {
                $newArray = $this->linkedinService->getAllCommentPost($message, $this->metadata['access_token']);
                if (array_key_exists('content', $newArray['updateContent']['companyStatusUpdate']['share'])) {
                    $this->imageType = App::makeWith(ImageTransform::class, ['messages' => $newArray]);
                    $this->messageImage = array_merge($this->imageType->getTransformedMessage(), $this->messageImage);
                } else
                    if (array_key_exists('comment', $newArray['updateContent']['companyStatusUpdate']['share'])) {
                        $this->commentType = App::makeWith(CommentTransform::class, ['messages' => $newArray]);
                        $this->messageComment = array_merge($this->messageComment, $this->commentType->getTransformedMessage());
                    } else {
                        Log::debug("video goes here");
                    }
            }
            $response = array_merge($this->messageComment, $this->messageImage);
            return $response;
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . 'redirect a messageType');
        }
    }

    /**
     * This method assure the correct format to send the array to zendesk by sorted the
     * array obtained in the  getTransformedMessage.
     * @param array $messages
     * @return array
     * @throws \Throwable
     */
    public function transformMessage($messages)
    {
        try {
            $messagesTransformed = $this->getTransformedMessage($messages['values']);
            $newMessagesTransformed = $this->sortMessages($messagesTransformed);
            return $response = $this->trackingMessage($newMessagesTransformed);
        } catch (\Exception $exception) {
            Log::error("Transformed Error: " . $exception->getMessage() . " Line:" . $exception->getLine() . 'problems to sorted message');
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
}