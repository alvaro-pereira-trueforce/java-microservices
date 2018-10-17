<?php

namespace APIServices\Zendesk_Instagram\Models\Factories;


use APIServices\Zendesk\Models\IMessageType;
use Illuminate\Support\Facades\App;
use ReflectionException;

class MessageTypeFactory
{
    /**
     * @param $message_type
     * @param $payload
     * @param $settings
     * @return IMessageType | null
     */
    static function getMessageType($message_type, $payload, $settings)
    {
        try {
            /** @var IMessageType $message */
            $message_type_class = App::makeWith('instagram_' . $message_type, [
                'payload' => $payload,
                'settings' => $settings
            ]);
            return $message_type_class;
        } catch (ReflectionException $exception) {
            return null;
        } catch (\Exception $exception) {
            //Log::error($exception->getMessage());
            return null;
        }
    }
}