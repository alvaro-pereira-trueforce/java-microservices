<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Audio extends FilesMessageType {

    function getTransformedMessage() {
        $audio = $this->message->getAudio();
        try
        {
            if((int) $audio->getFileSize() > 5000000)
            {
                $this->telegramService->sendTelegramMessage(
                    $this->chat_id,
                    'Our support size limit was reached, please try to send smaller Audios'
                );
                return null;
            }
            $documentURL = $this->telegramService->getDocumentURL($audio);
            return $this->getBasicDocumentResponse('Music File Message', $documentURL);
        }catch (\Exception $exception)
        {
            return null;
        }
    }
}