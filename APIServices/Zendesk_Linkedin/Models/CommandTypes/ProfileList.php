<?php

namespace APIServices\Zendesk_Linkedin\Models\CommandTypes;

use Illuminate\Support\Facades\Log;

/**
 * Class ProfileList
 * @package APIServices\Zendesk_Linkedin\Commands
 */
class ProfileList extends CommandType
{
    /**
     * @throws \Throwable
     */
    function handleCommand()
    {
        Log::notice("this is the command ..." . $this->nameCommand);
        try {
            if (array_key_exists('values', $this->comment)) {
                foreach ($this->comment['values'] as $profile) {
                    if (array_key_exists('person', $profile)) {
                        $newProfileData=$this->getProfileFormat($profile);
                        $listProfile[] = $newProfileData;
                    }
                }
                if (!empty($listProfile)) {
                    $zendeskProfiles=$this->getUniqueProfile($listProfile);
                    $zendeskBody = $this->getZendeskResponseModel();
                    $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskBody, view('linkedin.commands.profile_viewer', [
                        'listProfiles' => $zendeskProfiles,
                        'message' => 'This post profiles'
                    ])->render());
                    $this->getZendeskAPIServiceInstance()->pushNewMessage($zendeskResponse);
                } else {
                    $response = $this->getZendeskDefaultModel('There is not records to show yet for this Command');
                    $this->getZendeskAPIServiceInstance()->pushNewMessages($response);
                }
            }
        } catch (\Exception $exception) {
            Log::error('Message: ' . $exception->getMessage() . ' On Line: ' . $exception->getLine());
            $response = $this->getZendeskDefaultModel('There is not records to show yet for this Command');
            $this->getZendeskAPIServiceInstance()->pushNewMessages($response);
        }
    }
}



