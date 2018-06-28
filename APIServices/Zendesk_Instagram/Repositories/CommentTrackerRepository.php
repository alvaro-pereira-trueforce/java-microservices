<?php

namespace APIServices\Zendesk_Instagram\Repositories;

use APIServices\Zendesk\Models\CommentTrack;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Support\Facades\App;

class CommentTrackerRepository extends RepositoryUUID {

    /**
     * @return CommentTrack
     */
    protected function getModel() {
        return App::make(CommentTrack::class);
    }

    /**
     * @param $post_id
     * @return mixed
     * @throws \Exception
     */
    public function findByPostID($post_id) {
        try {
            $model = $this->getModel();
            return $model->where('post_id', '=', $post_id)->first();
        } catch (\Exception $exception) {
            throw $exception;
        }

    }

    /**
     * Delete the registered last comment date by post_id
     *
     * @param $post_id
     * @return mixed
     * @throws \Exception
     */
    public function deleteByPostID($post_id) {
        try {
            $model = $this->findByPostID($post_id);
            if ($model) {
                return $model->delete();
            }
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     * @throws \Exception
     */
    public function update(Model $model, array $data)
    {
        try {
            $model->fill($data);
            $model->save();
            return $model;
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }

    /**
     * @param array $data
     * @return CommentTrack
     * @throws \Exception
     */
    public function create(array $data)
    {
        try {
            $model = $this->getModel();
            $model->fill($data);
            $model->save();
            return $model;
        } catch (\Exception $exception) {
            throw  $exception;
        }
    }
}