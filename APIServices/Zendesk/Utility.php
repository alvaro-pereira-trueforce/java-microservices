<?php

namespace APIServices\Zendesk;

class Utility {
    public function getExternalID(array $data)
    {
        $result = "";
        foreach ($data as $item)
        {
            $result == "" ? $result = $item : $result = $result.':'.$item;
        }
        return $result;
    }
}