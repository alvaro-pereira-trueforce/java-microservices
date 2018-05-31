<?php

namespace APIServices\Zendesk\Models\Utils\Instagram;

use APIServices\Instagram\Services\InstagramService;
use APIServices\Zendesk\Models\Formatters\Instagram\PostFormatter;
use Illuminate\Support\Facades\App;
use Carbon\Carbon;

class Post implements ITransformer
{
    protected $owner;
    protected $posts;
    protected $instagramService;
    protected $chanel_type;
    protected $state;

    public function __construct($owner, $posts, $state, InstagramService $instagramService)
    {
        $this->owner = $owner;
        $this->posts = $posts;
        $this->state = $state;
        $this->instagramService = $instagramService;
        $this->chanel_type = "INSTAGRAM";
    }

    /**
     * @return array
     */
    public function generateToTransformedMessage()
    {
        $transformedMessages = [];
        $postsIdToComments = [];
        foreach ($this->posts as $post) {
            $post_id = $post['id'];
            $post_timestamp = date("c", strtotime($post['timestamp']));
            if ($this->expire($post_timestamp)) {
                $this->instagramService->removePost($post_id);
                continue;
            }
            if ($post_timestamp > $this->state['last_post_date']) {
                $transformedPosts = $this->getUpdatesPosts($post);
                if ($transformedPosts != null) {
                    array_push($transformedMessages, $transformedPosts);
                    array_push($postsIdToComments, $post_id);
                }
            } else {
                array_push($postsIdToComments, $post_id);
            }
        }
        return [
            'transformedMessages' => $transformedMessages,
            'state' =>  $post_timestamp,
            'postIdToComments' => $postsIdToComments];
    }

    /**
     * @param $date
     * @return bool
     */
    private function expire($date)
    {
        $date = new Carbon($date);
        return $date->diffInMinutes(Carbon::now()) > (int)env('TIME_EXPIRE_FOR_TICKETS_IN_MINUTES_INSTAGRAM');
    }

    /**
     * @param $post
     * @return array | null
     */
    private function getUpdatesPosts($post)
    {
        try {
            /** @var PostFormatter $formatter */
            $formatter = App::makeWith($this->chanel_type . '.' . $post['media_type'], [
                'owner' => $this->owner,
                'post' => $post

            ]);
            return $formatter->getTransformedMessage();
        } catch (\Exception $exception) {
            return null;
        }
    }
}