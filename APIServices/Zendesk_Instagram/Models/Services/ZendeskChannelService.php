<?php

namespace APIServices\Zendesk_Instagram\Models\Services;


use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;

class ZendeskChannelService
{

    protected $instargam_service;
    protected $zendeskUtils;
    protected $chanel_type;

    public function __construct(InstagramService $instagramService, Utility $zendeskUtils)
    {
        $this->instargam_service = $instagramService;
        $this->zendeskUtils = $zendeskUtils;
        $this->chanel_type = 'instagram';
    }

    function pullState($uuid)
    {
        $updated_at = $this->instargam_service->getUpdatedAt($uuid);
        if ($updated_at == null) {
            return [];
        }
        $current_date = gmdate('Y-m-d\TH:i:s\Z', $updated_at->getTimestamp());
        return ['most_recent_item_timestamp' => sprintf('%s', $current_date)];
    }

    /**
     * @param $metadata
     * @return array
     */
    public function getUpdates($uuid, $state_date)
    {
        $updates = $this->instargam_service->getInstagramUpdatesMedia($uuid);
        $transformedMessages = [];
        foreach ($updates as $update) {
            if (count($transformedMessages) > 199) {
                break;
            }
            $post_time = $update['created_time'];
            $aux = gmdate('Y-m-d\TH:i:s\Z', $post_time);
            if ($aux > $state_date) {
                //$content_type = $this->telegram_service->detectMessageType($update);
                array_push($transformedMessages, $this->pushMedia($update, null));
                $count_comments = $update['comments'];
                if ($count_comments['count'] > 0) {
                    $post_id = $update['id'];
                    $comments = $this->instargam_service->getInstagramUpdatesComments($uuid, $post_id);
                    foreach ($comments as $comment) {
                        if (count($transformedMessages) > 199) {
                            break;
                        }
                        array_push($transformedMessages, $this->pushComment($update, $comment));
                    }
                }
            }
        }
        return $transformedMessages;
    }

    public function pushMedia($update, $contentType)
    {
        $post_time = $update['created_time'];
        $post_id = $update['id'];
        $link = $update['link'];
        //data User
        $user = $update['user'];
        $user_name_post = $user['username'];
        $link_profile_picture_post = $user['profile_picture'];
        //Images of Post
        $images = $update['images'];
        $standard_resolution = $images['standard_resolution'];
        if ($update['caption'] != null) {
            $caption = $update['caption'];
            $post_text = $caption['text'];
        } else {
            //Name to ticket
            $post_text = $user_name_post . ' Posted a photo';
        }
        return [
            'external_id' => $this->zendeskUtils->getExternalID([$post_id]),
            'message' => $post_text,
            'html_message' => sprintf('<p><img src=%s></p>', $standard_resolution['url']),
            'thread_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
            'created_at' => gmdate('Y-m-d\TH:i:s\Z', $post_time),
            'author' => [
                'external_id' => $this->zendeskUtils->getExternalID([$post_id, $user_name_post]),
                'name' => $user_name_post,
                "image_url" => $link_profile_picture_post
            ]
        ];
    }

    public function pushComment($update, $comment)
    {
        $link = $update['link'];
        $post_id = $update['id'];
        $comment_id = $comment['id'];
        $user_name = $comment['from']['username'];
        $comment_time = $comment['created_time'];
        $comment_text = $comment['text'];
        return [
            'external_id' => $this->zendeskUtils->getExternalID([$comment_id]),
            'message' => $comment_text,
            'thread_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
            'created_at' => gmdate('Y-m-d\TH:i:s\Z', $comment_time),
            'author' => [
                'external_id' => $this->zendeskUtils->getExternalID([$comment_id, $user_name]),
                'name' => $user_name
            ]
        ];
    }
}