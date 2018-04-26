<?php

namespace APIServices\Instagram\Services;

use APIServices\Instagram\Logic\InstagramLogic;
use APIServices\Instagram\Repositories\InstagramRepository;
use APIServices\Zendesk\Utility;
use Illuminate\Support\Facades\Log;

use APIServices\Instagram\Repositories\ChannelRepository;
use Illuminate\Database\DatabaseManager;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Events\Dispatcher;



class InstagramService
{
    protected $database;

    protected $dispatcher;

    protected $repository;

    protected $instagramAPI;

    protected $zendeskUtils;

    public function __construct(

        DatabaseManager $database,
        Dispatcher $dispatcher,
        InstagramRepository $repository,
        InstagramLogic $instagramLogic,
        Utility $zendeskUtils
    )
    {


        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->instagramAPI = $instagramLogic;
        $this->zendeskUtils = $zendeskUtils;

        $this->instagramAPI->setApiKey('c133bd0821124643a3a0b5fbe77ee729');
        $this->instagramAPI->setApiSecret('308973f7f4944f699a223c74ba687979');
        $this->instagramAPI->setApiCallback('https://twitter.com/soysantizeta');
    }

    public function getAll($options = []) {
        return $this->repository->get($options);
    }

    public function create($data) {
        $user = $this->repository->create($data);
        return $user;
    }


    public function update($uuid, array $data) {
        $model = $this->getRequestedModel($uuid);

        $this->repository->update($model, $data);

        return $model;
    }

    public function delete($uuid) {
        $model = $this->getById($uuid);
        return $model->delete();
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

    private function getRequestedModel($uuid) {
        $model = $this->repository->getByUUID($uuid);

        if (is_null($model)) {
            throw new ModelNotFoundException();
        }
        return $model;
    }
    public function getById($uuid, array $options = []) {
        $model = $this->getRequestedModel($uuid);

        return $model;
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
        $instagramModel = $this->repository->getByUUID($uuid);
        if ($instagramModel == null) {
            return [];
        }
        try {
            //$telegram = $this->getTelegramInstance($instagramModel->token);
            $this->instagramAPI->setAccessToken($instagramModel->token);
            Log::info( $this->instagramAPI->getAccessToken());
            $updates = array($this->instagramAPI->getUserMedia($auth = true, $id = 'self', $limit = 0));
            $updates = json_decode(json_encode($updates), True);
            $updates_data = $updates[0]['data'];
            $transformedMessages = [];
            $count_comment = 0;
            $count_post = 0;
            foreach ($updates_data as $update) {
                $count_post++;
                $post_id = $update['id'];
                $link = $update['link'];
                //$comment_id = $comment['id'];
                //$user_name = $comment['from']['username'];
                $user = $update['user'];
                $user_name_post = $user['username'];
                $post_time = $update['created_time'];


                if ($update['caption']!=null){
                    $caption = $update['caption'];
                    $post_text = $caption['text'];
                }else{
                    //Name to ticket
                    $post_text = $user_name_post . ' Posted a photo';
                    //Images
                    $images = $update['images'];
                    $standard_resolution = $images['standard_resolution'];
                    //Push post
                    array_push($transformedMessages, [
                        'external_id' => $this->zendeskUtils->getExternalID([$post_id]),
                        'message' => $post_text,
                        'html_message'=>sprintf('<p><img src=%s></p>',$standard_resolution['url']),
                        'thread_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
                        'created_at' => gmdate('Y-m-d\TH:i:s\Z', $post_time),
                        'author' => [
                            'external_id' => $this->zendeskUtils->getExternalID([$post_id, $user_name_post]),
                            'name' => $user_name_post,
                            "image_url"=> $standard_resolution['url']

                        ]
                    ]);
//                    //Push photo
//                    $images = $update['images'];
//                    $standard_resolution = $images['standard_resolution'];
//                    array_push($transformedMessages, [
//                        'external_id' => $this->zendeskUtils->getExternalID([$link,$post_id]),
//                        'message' => file_get_contents($standard_resolution['url']),
//                        'thread_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
//                        'created_at' => gmdate('Y-m-d\TH:i:s\Z', $post_time),
//                        'author' => [
//                            'external_id' => $this->zendeskUtils->getExternalID([$post_id, $user_name_post]),
//                            'name' => $user_name_post
//                        ]
//                    ]);
                }
                //Push post
                array_push($transformedMessages, [
                    'external_id' => $this->zendeskUtils->getExternalID([$post_id]),
                    'message' => $post_text,
                    'thread_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
                    'created_at' => gmdate('Y-m-d\TH:i:s\Z', $post_time),
                    'author' => [
                        'external_id' => $this->zendeskUtils->getExternalID([$post_id, $user_name_post]),
                        'name' => $user_name_post
                    ]
                ]);
                //
                $count_comments = $update['comments'];
                if($count_comments['count']>0){
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
                            'external_id' => $this->zendeskUtils->getExternalID([$comment_id]),
                            'message' => $comment_text,
                            'thread_id' => $this->zendeskUtils->getExternalID([$link, $post_id]),
                            'created_at' => gmdate('Y-m-d\TH:i:s\Z', $comment_time),
                            'author' => [
                                'external_id' => $this->zendeskUtils->getExternalID([$comment_id, $user_name]),
                                'name' => $user_name
                            ]
                        ]);
                    }
                }
               Log::info($update);
            }
            return $transformedMessages;

        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    public function registerNewIntegration($name, $token, $subdomain) {
        try {
            $model = $this->repository->create([
                'token' => $token,
                'zendesk_app_id' => $subdomain,
                'integration_name' => $name
            ]);
            return [
                'token' => $model->uuid,
                'integration_name' => $model->integration_name,
                'zendesk_app_id' => $model->zendesk_app_id
            ];
        } catch (QueryException $exception) {
            return ["error" => ""];
        } catch (\Exception $exception) {
            Log::info($exception);
            return null;
        }
    }

    public function getMetadataFromSavedIntegration($uuid) {
        $current_channel = $this->getById($uuid);
        return [
            'token' => $current_channel->uuid,
            'integration_name' => $current_channel->integration_name,
            'zendesk_app_id' => $current_channel->zendesk_app_id
        ];
    }

    public function getByZendeskAppID($subdomain) {
        $model = $this->repository->getModel();
        $channels = $model->where('zendesk_app_id', '=', $subdomain)->get();
        return $channels;
    }

    public function sendInstagramMessage($chat_id, $user_id, $uuid, $message) {
        $instagramModel = $this->repository->getByUUID($uuid);
        if ($instagramModel == null) {
            return "";
        }

        try {
            $instagram = $this->getInstagramInstance($instagramModel->token);

//            $response = $instagram->sendMessage([
//                'chat_id' => $chat_id,
//                'text' => $message
//            ]);
//
//            $message_id = $response->getMessageId();
//            $user_id = $response->getFrom()->get('id');
//            $chat_id = $response->getChat()->get('id');
//            return $this->zendeskUtils->getExternalID([$user_id, $chat_id, $message_id]);
              return null;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return "";
        }
    }
}
