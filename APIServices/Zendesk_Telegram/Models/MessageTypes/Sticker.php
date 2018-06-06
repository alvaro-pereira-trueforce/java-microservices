<?php

namespace APIServices\Zendesk_Telegram\Models\MessageTypes;


class Sticker extends FilesMessageType
{
    /**
     * @return array
     * @throws \Exception
     * @throws \Throwable
     */
    function getTransformedMessage()
    {
        try
        {
            $stickerSizes = $this->message->getSticker();
            $stickerURL = $this->telegramService->getDocumentURL($stickerSizes['file_id']);
            $message = $this->getValidCaptionMessage('Sticker');
            $link = $this->getLocalURLFromExternalURL($stickerURL, $stickerSizes['file_id']);
            $basic_response = $this->zendeskUtils->getBasicResponse(
                $this->getExternalID(),
                $message,
                'thread_id',
                $this->parent_id,
                $this->message_date,
                $this->getAuthorExternalID(),
                $this->getAuthorName());
            $response = $this->zendeskUtils->addHtmlMessageToBasicResponse($basic_response,
                view('telegram.sticker_viewer', [
                    'title' => '',
                    'stickerURL' => env('APP_URL') . $link,
                    'message' => $message
                ])->render()
            );
            return $response;
        }catch (\Exception $exception)
        {
            throw $exception;
        }
    }
}