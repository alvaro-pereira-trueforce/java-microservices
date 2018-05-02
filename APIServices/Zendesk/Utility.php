<?php

namespace APIServices\Zendesk;

class Utility {
    public function getExternalID(array $data) {
        $result = "";
        foreach ($data as $item) {
            $result == "" ? $result = $item : $result = $result . ':' . $item;
        }
        return $result;
    }

    public function getBasicResponse($external_id, $message, $message_replay_type, $parent_id,
                                     $message_date, $author_external_id, $author_name) {
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
    }

    public function addHtmlMessageToBasicResponse(array $basic_response, $html_message) {
        $basic_response['html_message'] = $html_message;
        return $basic_response;
    }

    public function addFilesURLToBasicResponse(array $basic_response, array $files_urls)
    {
        $basic_response['file_urls'] = $files_urls;
        return $basic_response;
    }
}