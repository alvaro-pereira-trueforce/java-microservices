<?php

namespace APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter;

use Illuminate\Support\Facades\Log;

/**
 * Class Timestamp
 * @package APIServices\Zendesk_Linkedin\MessagesBuilder\MessageFilter
 */
class Timestamp extends MessageFilter
{
    /**
     * @param $treads
     * @return string
     * @throws \Exception
     */
    function getTransformedMessage($treads)
    {
        try {
            $idAuthor=explode(':', $treads);
            $response['timeLimit'] = gmdate('Y-m-d\TH:i:s\Z', $this->comment['updateContent']['companyStatusUpdate']['share']['timestamp'] / 1000);
            $response['nameAuthor']=$this->comment['updateContent']['company']['name'];
            $response['idAuthor']=strval($idAuthor[1]);
            return $response;
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
            throw $exception;
        }
    }
}