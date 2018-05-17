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

    protected $zendeskUtils;

    public function __construct(
        DatabaseManager $database,
        Dispatcher $dispatcher,
        InstagramRepository $repository,
        Utility $zendeskUtils
    )
    {
        $this->database = $database;
        $this->dispatcher = $dispatcher;
        $this->repository = $repository;
        $this->zendeskUtils = $zendeskUtils;
    }

    public function getAll($options = [])
    {
        return $this->repository->get($options);
    }

    public function create($data)
    {
        $user = $this->repository->create($data);
        return $user;
    }


    public function update($uuid, array $data)
    {
        $model = $this->getRequestedModel($uuid);

        $this->repository->update($model, $data);

        return $model;
    }

    public function delete($uuid)
    {
        $model = $this->getById($uuid);
        return $model->delete();
    }

    /**
     * @param $uuid
     * @return null|Api
     */
    private function getInstagramActiveInstanse($uuid)
    {
        return $this->getInstagramInstance($this->getTokenFromUUID($uuid));
    }

    private function getRequestedModel($uuid)
    {
        $model = $this->repository->getByUUID($uuid);

        if (is_null($model)) {
            throw new ModelNotFoundException();
        }
        return $model;
    }

    public function getById($uuid, array $options = [])
    {
        $model = $this->getRequestedModel($uuid);
        return $model;
    }

    /**
     * @param $uuid
     * @return string token
     */
    private function getTokenFromUUID($uuid)
    {
        $telegramModel = $this->repository->getByUUID($uuid);

        if ($telegramModel == null) {
            return "";
        }
        return $telegramModel->token;
    }

    public function getUpdatedAt($uuid)
    {
        $instagramModel = $this->repository->getByUUID($uuid);
        if ($instagramModel == null) {
            return null;
        }
        return $instagramModel->updated_at;
    }

    public function getInstagramUpdatesMedia($uuid)
    {
        try {
            $instagram = $this->getInstagramActiveInstanse($uuid);
            $updates = array($instagram->getUserMedia($auth = true, $id = 'self', $limit = 0));
            $updates = json_decode(json_encode($updates), True);
            $updates_data = $updates[0]['data'];
            return $updates_data;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    public function getInstagramUpdatesComments($uuid, $post_id)
    {
        try {
            $instagram = $this->getInstagramActiveInstanse($uuid);
            $comments = array($instagram->getMediaComments($post_id, true));
            $comments = json_decode(json_encode($comments), True);
            $comments_data = $comments[0]['data'];
            return $comments_data;
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            return [];
        }
    }

    public function registerNewIntegration($name, $token, $subdomain)
    {
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

    public function getMetadataFromSavedIntegration($uuid)
    {
        $current_channel = $this->getById($uuid);
        return [
            'token' => $current_channel->uuid,
            'integration_name' => $current_channel->integration_name,
            'zendesk_app_id' => $current_channel->zendesk_app_id
        ];
    }

    public function getByZendeskAppID($subdomain)
    {
        $model = $this->repository->getModel();
        $channels = $model->where('zendesk_app_id', '=', $subdomain)->get();
        return $channels;
    }

    public function sendInstagramMessage($post_id, $uuid, $message)
    {
        $instagramModel = $this->repository->getByUUID($uuid);
        if ($instagramModel == null) {
            return "";
        }
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
