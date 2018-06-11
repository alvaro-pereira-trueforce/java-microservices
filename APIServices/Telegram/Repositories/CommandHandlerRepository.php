<?php

namespace APIServices\Telegram\Repositories;


use APIServices\Telegram\Models\CommandHandler;
use App\Database\Eloquent\ModelUUID;
use App\Database\Eloquent\RepositoryUUID;
use Illuminate\Support\Facades\App;

class CommandHandlerRepository extends RepositoryUUID
{

    protected function getModel()
    {
        return App::make(CommandHandler::class);
    }

    /**
     * @param $user_id
     * @param $chat_id
     * @return ModelUUID
     * @throws \Exception
     */
    function getCommandWithUserAndChat($user_id, $chat_id)
    {
        try{
            $model = $this->getModel();
            return $model->where('chat_id', '=', $chat_id)
                ->where('user_id', '=', $user_id)->first();
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }

    /**
     * @param $user_id
     * @param $chat_id
     * @param $command
     * @param $state
     * @param $content
     * @return ModelUUID
     * @throws \Exception
     */
    function getCommandProcess($user_id, $chat_id, $command, $state, $content = '')
    {
        try
        {
            $model = $this->getModel();
            return $model->updateOrCreate([
                'user_id' => $user_id,
                'chat_id' => $chat_id
            ],[
                'command' => $command,
                'state' => $state,
                'content' => $content
            ]);
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }
}