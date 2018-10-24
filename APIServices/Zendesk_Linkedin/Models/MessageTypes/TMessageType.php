<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

use Illuminate\Support\Facades\Log;
use APIServices\LinkedIn\Services;

/**
 * Class TMessageType
 * This class will retrieve a TMessageType Model class
 */
class TMessageType extends MessageType
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
     * TMessageType constructor.
     * @param CommentType $commentType
     * @param ImageType $imageType
     * @param Services\LinkedinService $linkedinService
     */
    public function __construct(CommentType $commentType, ImageType $imageType, Services\LinkedinService $linkedinService)
    {
        $this->commentType = $commentType;
        $this->imageType = $imageType;
        $this->linkedinService = $linkedinService;
    }

    /**
     * tracking and return an array already transformed all the Comments,
     * Images and Videos into a zendesk format
     * @param $messages
     * @param $access_token
     * @return mixed|null
     * @throws \Throwable
     */
    function getTransformedMessage($messages, $access_token)
    {
        try {
            foreach ($messages as $message) {
                $newArray = $this->linkedinService->getAllCommentPost($message, $access_token);
                if (array_key_exists('content', $newArray['updateContent']['companyStatusUpdate']['share'])) {
                    $this->messageImage = array_merge($this->imageType->getTransformedMessage($newArray, $access_token), $this->messageImage);
                } else
                    if (array_key_exists('comment', $newArray['updateContent']['companyStatusUpdate']['share'])) {
                        $this->messageComment = array_merge($this->messageComment, $this->commentType->getTransformedMessage($newArray, $access_token));
                    } else {
                        Log::debug('here appeared video section');
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
     * @param $access_token
     * @return array
     * @throws \Throwable
     */
    public function transformMessage(array $messages, $access_token)
    {
        try {
            $messagesTransformed = $this->getTransformedMessage($messages['values'], $access_token);
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
        return collect($messagesTransformed)->sortBy('count')->reverse()->toArray();
    }

}