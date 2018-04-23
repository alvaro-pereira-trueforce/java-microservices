<?php

namespace APIServices\Instagram\Services;

use APIServices\Instagram\Logic\InstagramLogic;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;


class InstagramService
{

    protected $instagramAPI;

    protected $zendeskUtils;

    public function __construct(
        InstagramLogic $instagramLogic,
        Utility $zendeskUtils
    )
    {
        $this->instagramAPI = $instagramLogic;
        $this->zendeskUtils = $zendeskUtils;

        $this->instagramAPI->setApiKey('c133bd0821124643a3a0b5fbe77ee729');
        $this->instagramAPI->setApiSecret('308973f7f4944f699a223c74ba687979');
        $this->instagramAPI->setApiCallback('https://twitter.com/soysantizeta');
    }

    public function getById($uuid, array $options = [])
    {
        return null;
    }

    /**
     * @param $token
     * @return Api
     */
    private function getInstagramInstance($token)
    {
        $this->instagramAPI->setAccessToken($token);
        return $this->instagramAPI;
    }

    /**
     * Get updates return all the messages from telegram converting the data for zendesk channel
     * pulling service.
     *
     * @param string $uuid TelegramChannelUUID to retrieve the information from the database
     * @return array Zendesk External resources
     */
    public function getInstagramUpdates($uuid)
    {

        try {
            $this->instagramAPI->setAccessToken($uuid);
            $updates = array($this->instagramAPI->getUserMedia($auth = true, $id = 'self', $limit = 0));
            $updates = json_decode(json_encode($updates), True);
            $updates_data = $updates[0]['data'];
            $transformedMessages = [];
            $count_comment = 0;
            foreach ($updates_data as $update) {
                $post_id = $update['id'];
                $link = $update['link'];
                // Call comment
                $comments = array($this->instagramAPI->getMediaComments($post_id, true));
                $comments = json_decode(json_encode($comments), True);
                $comments_data = $comments[0]['data'];

                foreach ($comments_data as $comment) {
                    $count_comment++;
                    $comment_id = $comment['id'];
                    $user_name = $comment['from']['username'];
                    $comment_time = $comment['created_time'];
                    $comment_text = $comment['text'];

                    // must have a buffer in the future to catch only the first 200 messages and send
                    // it the leftover later. Maybe never happen an overflow.
                    if ($count_comment > 199) {
                        break;
                    }
                    array_push($transformedMessages, [
                        'external_id' => $this->zendeskUtils->getExternalID([$link, $post_id, $comment_id]),
                        'message' => $comment_text,
                        'parent_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
                        'created_at' => gmdate('Y-m-d\TH:i:s\Z', $comment_time),
                        'author' => [
                            'external_id' => $this->zendeskUtils->getExternalID([$comment_id, $user_name]),
                            'name' => $user_name
                        ]
                    ]);
                }
                Log::info($update);
            }
            return $transformedMessages;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }
}
