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

                                    /** Because the facebook Validation the type field strategy only works with the transformed message format, here we need to set and if else logic */
                                    if (!empty($change['value']['id']) && !empty($change['value']['text']) && !empty($change['value']['media'])) {
                                        $payload = [
                                            'id' => $change['value']['id'],
                                            'text' => $change['value']['text'],
                                            'media' => $change['value']['media']
                                        ];
                                        ProcessInstagramEvent::dispatch($instagramChannel, $field_type, $payload, 1);
                                    }
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

    // Just for tests we can make a unit test with this steps
    /*
    public function test(Request $request, ChannelRepository $channelRepository, ZendeskChannelService $channelService)
    {
        $payload = [
            'id' => $request->field_id,
            'text' => "Test",
            'media' => [
                'id' => $request->field_media
            ]
        ];
        $instagramChannel = $channelRepository->getModelByColumnName('instagram_id', $request->instagram_id);
        $job = new ProcessInstagramEvent($instagramChannel, $request->field_type, $payload);
        $job->handle($channelService);
        dd('end');
    }*/
}