<?php

namespace APIServices\Zendesk_Instagram\Models\WebHooks;


use APIServices\Facebook\Services\FacebookService;
use APIServices\Zendesk\Models\IMessageType;
use APIServices\Zendesk\Utility;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class CommentPayload extends MessageType
{
    protected $facebookService;
    protected $comment;
    protected $media;
    protected $parent;
    protected $settings;
    protected $utility;

    /**
     * CommentPayload constructor.
     * @param $field_id
     * @param $settings
     * @param FacebookService $facebookService
     * @param Utility $utility
     * @throws \Exception
     */
    public function __construct($field_id, $settings, FacebookService $facebookService, Utility $utility)
    {
        parent::__construct($field_id);
        try {
            $this->utility = $utility;
            $this->facebookService = $facebookService;
            $this->comment = $facebookService->getInstagramCommentByID($field_id);
            $this->parent = $facebookService->getParentFromComment($this->comment['media']['id'], $field_id);
            $this->media = $facebookService->getInstagramMediaByID($this->comment['media']['id']);
            $this->settings = $settings;
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @return array
     * @throws \Exception
     */
    function getTransformedMessage()
    {
        try {
            $media_date = Carbon::parse($this->media['timestamp']);
            if ($media_date->diffInMinutes(Carbon::now()) >= (int)env('TIME_EXPIRE_FOR_TICKETS_IN_MINUTES_INSTAGRAM')) {
                Log::debug('The comment is omitted because is Old: ' . $media_date);
                return [];
            }
            return $this->getBasicResponse();
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    function getAuthorExternalID()
    {
        try {
            return $this->comment['username'];
        } catch (\Exception $exception) {
            return 'Unknown';
        }
    }

    function getExternalID()
    {
        try {
            return $this->utility->getExternalID([
                $this->getParentID(),
                $this->comment['id']
            ]);
        } catch (\Exception $exception) {
            return uniqid();
        }
    }

    function getAuthorName()
    {
        try {
            return $this->comment['username'];
        } catch (\Exception $exception) {
            return 'Unknown';
        }
    }

    function getParentID()
    {
        try {
            return $this->utility->getExternalID([
                $this->media['id'],
                $this->media['username']
            ]);
        } catch (\Exception $exception) {
            return uniqid();
        }
    }

    function getMediaExternalID()
    {
        try {
            return $this->utility->getExternalID([
                'media',
                $this->getParentID()
            ]);
        } catch (\Exception $exception) {
            return uniqid();
        }
    }

    function getMediaAuthorName()
    {
        try {
            return $this->media['username'];
        } catch (\Exception $exception) {
            return 'Unknown';
        }
    }

    function getMediaAuthorExternalID()
    {
        try {
            return $this->media['username'];
        } catch (\Exception $exception) {
            return 'Unknown';
        }
    }

    function getBasicResponse()
    {
        try {
            $created_at_comment = date("c", strtotime($this->comment['timestamp']));

            /** @var IMessageType $media_type */
            $media_type = App::makeWith('instagram_' . $this->media['media_type'], [
                'media' => $this->media,
                'comment' => $this->comment
            ]);

            $basic_media_response = $media_type->getTransformedMessage();

            $basic_comment_response = $this->utility->getBasicResponse(
                $this->getExternalID(),
                $this->comment['text'],
                'thread_id',
                $this->getParentID(),
                $created_at_comment,
                $this->getAuthorExternalID(),
                $this->getAuthorName()
            );

            if ($this->parent)
                $basic_comment_response = $this->utility->addHtmlMessageToBasicResponse($basic_comment_response,
                    view('instagram.multimedia.reply_viewer', [
                        'reply' => $this->comment['text'],
                        'comment_to_reply' => $this->parent['text']
                    ])->render()
                );

            $response = [];
            if (!empty($basic_media_response))
                array_push($response, $this->utility->addCustomFieldsArray($basic_media_response, $this->settings));

            if (!empty($basic_comment_response))
                array_push($response, $this->utility->addCustomFieldsArray($basic_comment_response, $this->settings));

            return $response;

        } catch (\Exception $exception) {
            throw $exception;
        }
    }


    function getFooterPage()
    {
        if (array_key_exists('caption', $this->media)) {
            return $this->media['caption'];
        }
        $media_type = ucfirst(strtolower($this->media['media_type']));
        return $media_type . ' was posted by ' . $this->comment['username'];
    }
}