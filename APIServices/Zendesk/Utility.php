<?php

namespace APIServices\Zendesk;

use Illuminate\Support\Facades\Log;

class Utility
{
    /**
     * @param array $data
     * @return mixed|string
     */
    public function getExternalID(array $data)
    {
        $result = "";
        foreach ($data as $item) {
            $result == "" ? $result = $item : $result = $result . ':' . $item;
        }
        return $result;
    }

    /**
     * @param $external_id
     * @param $message
     * @param $message_replay_type
     * @param $parent_id
     * @param $message_date
     * @param $author_external_id
     * @param $author_name
     * @return array
     */
    public function getBasicResponse($external_id, $message, $message_replay_type, $parent_id,
                                     $message_date, $author_external_id, $author_name)
    {
        try {
            return [
                'external_id' => $external_id,
                'message' => $message,
                $message_replay_type => $parent_id,
                'created_at' => gmdate('Y-m-d\TH:i:s\Z', $message_date),
                'author' => [
                    'external_id' => $author_external_id,
                    'name' => $author_name
                ]
            ];
        } catch (\Exception $exception) {
            return [
                'external_id' => $external_id,
                'message' => $message,
                $message_replay_type => $parent_id,
                'created_at' => $message_date,
                'author' => [
                    'external_id' => $author_external_id,
                    'name' => $author_name
                ]
            ];
        }
    }

    /**
     * @param array $basic_response
     * @param $html_message
     * @return array
     */
    public function addHtmlMessageToBasicResponse(array $basic_response, $html_message)
    {
        $basic_response['html_message'] = $html_message;
        return $basic_response;
    }

    /**
     * @param array $basic_response
     * @param array $files_urls
     * @return array
     */
    public function addFilesURLToBasicResponse(array $basic_response, array $files_urls)
    {
        $basic_response['file_urls'] = $files_urls;
        return $basic_response;
    }

    /**
     * @param array $basic_response
     * @param array $fields
     * @return array
     */
    public function addFields(array $basic_response, array $fields)
    {
        $basic_response['fields'] = $fields;
        return $basic_response;
    }

    /**
     * @param array $basic_response
     * @param array $settings
     * @return array
     */
    public function addCustomFieldsArray(array $basic_response, array $settings)
    {
        $fields = [];
        if (array_key_exists('ticket_priority', $settings)) {
            array_push($fields, [
                'id' => 'priority',
                'value' => $settings['ticket_priority']
            ]);
        }

        if (array_key_exists('ticket_type', $settings)) {
            array_push($fields, [
                'id' => 'type',
                'value' => $settings['ticket_type']
            ]);
        }

        //The tags must be saved as words separated with spaces in the model
        if (array_key_exists('tags', $settings)) {
            $tags = explode(' ', $settings['tags']);

            array_push($fields, [
                'id' => 'tags',
                'value' => $tags
            ]);
        }
        if (empty($fields))
            return $basic_response;

        return $this->addFields($basic_response, $fields);
    }
}