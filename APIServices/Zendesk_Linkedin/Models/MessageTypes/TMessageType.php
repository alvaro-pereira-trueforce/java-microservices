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
     * @param array $messages
     * @param $access_token
     * @return array
     * @throws \Throwable
     */
    public function transformMessage(array $messages, $access_token)
    {
        try {
            $loopHep = [];
            $indexNewMessage = [];
            $messageLoopTransformed = [];
            $response = $this->getTransformedMessage($messages['values'], $access_token);
            $array = collect($response)->sortBy('count')->reverse()->toArray();
            foreach ($array as $key => $indexNewMessage) {
                $messageLoopTransformed[] = $indexNewMessage;
                $indexNewMessage = array_merge($messageLoopTransformed, $loopHep);
            }
            return $indexNewMessage;
        } catch (\Exception $exception) {
            Log::error("Transformed Error: " . $exception->getMessage() . " Line:" . $exception->getLine() . 'problems to sorted message');
        }
    }


}