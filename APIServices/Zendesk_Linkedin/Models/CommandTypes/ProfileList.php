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
     * @return void
     * @throws \Throwable
     */
    function handleCommand()
    {
        Log::notice("this is the command ..." . $this->nameCommand);
        try {
            if (array_key_exists('values', $this->comment)) {
                foreach ($this->comment['values'] as $profile) {
                    if (array_key_exists('person', $profile)) {
                        $newProfileData = $this->getProfileFormat($profile);
                        $listProfile[] = $newProfileData;
                    }
                }
                if (!empty($listProfile)) {
                    $zendeskProfiles = $this->getUniqueProfile($listProfile);
                    $zendeskBody = $this->getZendeskModel('The following message respond the Command');
                    $zendeskResponse = $this->zendeskUtils->addHtmlMessageToBasicResponse($zendeskBody, view('linkedin.commands.profile_viewer', [
                        'listProfiles' => $zendeskProfiles,
                        'message' => 'This post profiles'
                    ])->render());
                    $this->getZendeskAPIServiceInstance()->pushNewMessage($zendeskResponse);
                } else {
                    $response = $this->getZendeskModel('There is not records to show yet for this Command');
                    $this->getZendeskAPIServiceInstance()->pushNewMessage($response);
                }
            } else {
                $response = $this->getZendeskModel('There is not records to show yet for this Command');
                $this->getZendeskAPIServiceInstance()->pushNewMessage($response);
            }
        } catch (\Exception $exception) {
            throw $exception;
        }
    }

    /**
     * @param $profile
     * @return mixed
     * @throws \Exception
     */
    public function getProfileFormat($profile)
    {
        try {
            $newProfileData['firstName'] = $profile['person']['firstName'];
            $newProfileData['lastName'] = $profile['person']['lastName'];
            $newProfileData['headline'] = $profile['person']['headline'];
            $newProfileData['siteStandardProfileRequest'] = $profile['person']['siteStandardProfileRequest']['url'];
            $newProfileData['id'] = $profile['person']['id'];
            return $newProfileData;
        } catch (\Exception $exception) {
            throw $exception;
        }

    }
}



