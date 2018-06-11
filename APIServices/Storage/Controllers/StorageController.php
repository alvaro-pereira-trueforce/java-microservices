<?php

namespace APIServices\Storage\Controllers;

use APIServices\Telegram\Services\TelegramService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class StorageController extends Controller {

    public function getFileToDownload($filename, Request $request) {
        try
        {
            $uuid = $request->uuid;
            $file_id = $request->id;

            /** @var TelegramService $telegramService */
            $telegramService = App::makeWith(TelegramService::class,[
                'uuid' => $uuid
            ]);

            $documentURL = $telegramService->getDocumentURL($file_id);
            if (strpos($filename, 'webp') !== false) {
                $documentURL = imagecreatefromwebp($documentURL);
                header('Content-Type: image/png');
                imagepng($documentURL);
                return null;
            }

            $headers = get_headers($documentURL, 1);
            return response()->streamDownload(function () use ($documentURL){
                echo file_get_contents($documentURL);
            }, $filename, $headers);

        }catch (\Exception $exception)
        {
            throw new BadRequestHttpException();
        }
    }
}
