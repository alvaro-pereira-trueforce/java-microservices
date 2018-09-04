<?php

namespace APIServices\Facebook\Controllers;

use APIServices\Facebook\Jobs\ProcessInstagramEvent;
use APIServices\Facebook\Requests\FacebookGetRequest;
use APIServices\Zendesk\Repositories\ChannelRepository;
use APIServices\Zendesk_Instagram\Models\InstagramChannel;
use APIServices\Zendesk_Instagram\Services\ZendeskChannelService;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class WebhookController extends Controller
{
    public function __construct()
    {
        App::when(ChannelRepository::class)->needs('$channelModel')->give(new InstagramChannel());
    }

    public function webhookSubscribe(FacebookGetRequest $request)
    {
        try {
            Log::debug("Subscribe:");
            Log::debug($request->all());
            $params = $request->all();
            if ($params['hub_verify_token'] == env('FACEBOOK_APP_SECRET'))
                return $params['hub_challenge'];
            else
                throw new UnauthorizedHttpException('Not Authorized');
        } catch (\Exception $exception) {
            Log::error($exception->getMessage());
            throw new UnauthorizedHttpException('Not Authorized');
        }
    }

    public function webhookHandler(Request $request, ChannelRepository $channelRepository)
    {
        try {
            $request = $request->all();
            Log::debug($request);
            if (array_key_exists('entry', $request)) {
                $entries = $request['entry'];
                foreach ($entries as $entry) {
                    if (array_key_exists('changes', $entry) && array_key_exists('id', $entry)) {
                        $instagramChannel = $channelRepository->getModelByColumnName('instagram_id', $entry['id']);
                        if ($instagramChannel) {
                            foreach ($entry['changes'] as $change) {
                                if (array_key_exists('field', $change) && array_key_exists('value', $change)) {
                                    $field_type = $change['field'];
                                    $field_id = $change['value']['id'];
                                    ProcessInstagramEvent::dispatch($instagramChannel, $field_type, $field_id)->onQueue('horizon');
                                }
                            }
                        }
                    }
                }
            }
            return response()->json('ok', 200);
        } catch (\Exception $exception) {
            throw new BadRequestHttpException();
        }
    }
}