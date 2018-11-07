<?php

namespace APIServices\Zendesk_Linkedin\Models\MessageTypes;

use Carbon\Carbon;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

/**
 * Class MessageTransform
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes
 */
abstract class MessageTransform implements IMessageTransform
{
    /**
     * @var $media
     */
    protected $media;
    /**
     * @var Utility
     */
    protected $zendeskUtils;
    /**
     * @var $messages
     */
    protected $messages;

    /**
     * MessageTransform constructor.
     * @param $messages
     * @param Utility $zendeskUtils
     */
    public function __construct(Utility $zendeskUtils, $messages)
    {
        $this->zendeskUtils = $zendeskUtils;
        $this->messages = $messages;
    }

    /**
     * tracking,linked and return the company_id/product_id with the LinkedIn profile who post a comment
     * @param $message
     * @return mixed
     */
    public function getExternalIdPost($message)
    {
        return $newExternalId = $message['updateContent']['companyStatusUpdate']['share']['id'] . ':' . $message['updateContent']['company']['id'] . ':' . $message['updateKey'];
    }

    /**
     * tracking and return the comment content post by a LinkedIn profile,
     * in case that the comment is not found return a comment by default
     * @param $message
     * @return mixed
     */
    public function getMessagePost($message)
    {
        if (!empty($message['updateContent']['companyStatusUpdate']['share']['comment'])) {
            return $message['updateContent']['companyStatusUpdate']['share']['comment'];
        } else {
            return 'Image was posted by ' . $message['updateContent']['company']['name'];
        }
    }

    /**
     * tracking, transform and return the creation date of one company's post
     * @param $message
     * @return mixed
     */
    public function getCreateAtPost($message)
    {
        return gmdate('Y-m-d\TH:i:s\Z', $message['updateContent']['companyStatusUpdate']['share']['timestamp'] / 1000);

    }

    /**
     * tracking and return the id and name of the post's author
     * @param $message
     * @return array
     */
    public function getAuthorPost($message)
    {
        return [
            'external_id' => strval($message['updateContent']['company']['id']),
            'name' => $message['updateContent']['company']['name'],
        ];
    }

    /**
     * return the Image section to attach the HTML features
     * @param $message
     * @return mixed
     */
    public function getUpdateMedia($message)
    {
        return $message['updateContent'];

    }

    /**
     * tracking and return the ImageURL
     * @param $media
     * @return mixed
     */
    public function getMediaImageUrl($media)
    {

        return $media['companyStatusUpdate']['share']['content']['submittedImageUrl'];
    }

    /**
     * @param $messages
     * @param $thead_id
     * @param $timeExpirationPost
     * @return array
     */
    public function getUpdateMediaType($messages, $thead_id, $timeExpirationPost)
    {
        try {
            if ((Carbon::createFromFormat('Y-m-d\TH:i:s\Z', $timeExpirationPost)->diffInSeconds($this->getExpirationTimePost($messages))) < env('LINKEDIN_TRACKING_EXPIRATION_TIME')) {
                if (array_key_exists('company', $messages)) {
                    return [
                        'external_id' => strval($messages['id']),
                        'message' => $messages['comment'],
                        'thread_id' => $thead_id,
                        'created_at' => gmdate('Y-m-d\TH:i:s\Z', $messages['timestamp'] / 1000),
                        'author' => [
                            'external_id' => strval($messages['company']['id']),
                            'name' => $messages['company']['name']
                        ]
                    ];
                } else if (array_key_exists('person', $messages)) {
                    $clientPost = $this->getUpdateMediaClientPost($messages, $thead_id);
                    return $clientPost;
                } else {
                    Log::debug('ImagePost');
                }
            } else {
                return [];
            }

        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine() . ' search image format error');

        }
    }

    /**
     * return a comment posted by a LinkedIn profile into a zendesk format
     * @param $message
     * @param $thead_id
     * @return array
     */
    public function getUpdateMediaClientPost($message, $thead_id)
    {
        $clientMessage = [
            'external_id' => strval($this->getExternalIdPostClient($message)),
            'message' => $this->getMessagePostClient($message),
            'created_at' => $this->getCreateAtPostClient($message),
            'thread_id' => $thead_id,
            'author' => $this->getAuthorPostClient($message)
        ];
        return $clientMessage;
    }

    /**
     * tracking and return the LinkedIn profile_id who posted a comment
     * @param $message
     * @return mixed
     */
    public function getExternalIdPostClient($message)
    {
        return $message['id'];
    }

    /**
     * tracking and return the LinkedIn profile_comment  who posted a comment
     * @param $message
     * @return mixed
     */
    public function getMessagePostClient($message)
    {
        return $message['comment'];
    }

    /**
     * tracking, transform  and return the LinkedIn profile creation date who posted a comment
     * @param $message
     * @return mixed
     */
    public function getCreateAtPostClient($message)
    {
        return gmdate('Y-m-d\TH:i:s\Z', $message['timestamp'] / 1000);
    }

    /**
     * tracking a return the LinkedIn profile personal information who posted a comment
     * @param $message
     * @return array
     */
    public function getAuthorPostClient($message)
    {
        return [
            'external_id' => strval($message['person']['id']),
            'name' => $message['person']['firstName'],
            'image_url' => $message['person']['pictureUrl']
        ];
    }

    /**
     * resolve a timestamp to be handle by the differSeconds carbon's method
     * @param $message
     * @return string
     */
    public function getExpirationTimePost($message)
    {

        return gmdate('Y-m-d\TH:i:s\Z', $message['timestamp'] / 1000);
    }

    /**
     * arrange all the comments according their timestamp
     * @param $messages
     * @return mixed
     */
    public function sortedComments($messages)
    {

        return collect($messages)->sortBy('created_at')->reverse()->toArray();

    }
}