<?php

namespace APIServices\Instagram\Services;

use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

use APIServices\Instagram\Repositories\ChannelRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Events\Dispatcher;


class InstagramService {
    protected $database;

    protected $dispatcher;

    protected $zendeskUtils;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        Utility $zendeskUtils
    ) {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->zendeskUtils = $zendeskUtils;
    }


    public function getInstagramUpdatesMedia($uuid) {
        try {
            return [];
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    public function getInstagramUpdatesComments($uuid, $post_id) {
        try {

            return [];
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    /**
     * @param $name
     * @param $token
     * @param $subdomain
     * @param $instagram_id
     * @param $page_id
     * @return array|null
     */
    public function registerNewIntegration($name, $token, $subdomain, $instagram_id, $page_id) {
        return json_encode([
            'integration_name' => $name,
            'zendesk_app_id' => $subdomain,
            'token' => $token,
            'instagram_id' => $instagram_id,
            'page_id' => $page_id
        ]);
    }

    public function sendInstagramMessage($post_id, $uuid, $message) {
        try {
            /*
            $array = array('text' => $message);
            $response = $this->instagramAPI->postUserMedia(true, $post_id, $array);
            $comments = json_decode(json_encode($response), True);
            $comment_data = $comments['data'];
            $comment_id = $comment_data['id'];
            return $this->zendeskUtils->getExternalID([$comment_id]);*/
            return '';
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }
}
