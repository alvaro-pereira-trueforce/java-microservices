<?php

namespace APIServices\Telegram\Repositories;

use APIServices\Telegram\Models\TelegramChannel;
use App\Database\Eloquent\Model;
use App\Database\Eloquent\Repository;
use Illuminate\Support\Facades\App;

class ChannelRepository extends Repository
{
    public function getModel()
    {
        return App::make(TelegramChannel::class);
    }
    /***
     * @param array $data ['token', 'zendesk_app_id']
     * @return TelegramChannel
     */
    public function create(array $data)
    {
        $user = $this->getModel();

        $user->fill($data);
        $user->save();

        return $user;
    }

    /***
     * @param Model $user
     * @param array           $data ['token', 'zendesk_app_id']
     * @return Model
     */
    public function update(Model $user, array $data)
    {
        $user->fill($data);
        $user->save();
        return $user;
    }
}
