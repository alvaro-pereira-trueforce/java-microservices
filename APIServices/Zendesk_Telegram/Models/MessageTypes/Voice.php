<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Voice extends FilesMessageType {

    function getTransformedMessage() {
        $voice = $this->message->getVoice();
        try
        {
            if((int) $voice->getFileSize() > 5000000)
            {
                $this->telegramService->sendTelegramMessage(
                    $this->chat_id,
                    'Our support size limit was reached (5MB), please try to send smaller Audios'
                );
                return null;
            }
            $documentURL = $this->telegramService->getDocumentURL($voice->getFileId());
            return $this->getBasicDocumentResponse('Message Voice', $documentURL, $voice->getFileId());
        }catch (\Exception $exception)
        {
            return null;
        }
    }
}