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
     * Get the last comment date by post_id
     *
     * @param $post_id
     * @return CommentTrack
     */
    public function findByPostID($post_id) {
        $model = $this->getModel();
        return $model->where('post_id', '=', $post_id)->first();
    }

    /**
     * Delete the registered last comment date by post_id
     * @param $post_id
     * @return bool
     */
    public function deleteByPostID($post_id) {
        try {
            $model = $this->findByPostID($post_id);
            if ($model) {
                return $model->delete();
            }
            return false;
        } catch (\Exception $exception) {
            return false;
        }
    }

    /**
     * @param Model $model
     * @param array $data
     * @return Model
     */
    public function update(Model $model, array $data)
    {
        $model->fill($data);
        $model->save();
        return $model;
    }

    /**
     * @param array $data
     * @return CommentTrack
     */
    public function create(array $data)
    {
        $model = $this->getModel();
        $model->fill($data);
        $model->save();
        return $model;
    }
}