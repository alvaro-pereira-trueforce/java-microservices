<?php
namespace APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes;

/**
 * Class EventType
 * @package APIServices\Zendesk_Linkedin\Models\MessageTypes\EventTypes
 */
abstract class EventType implements IEventType
{
    /**
     * get the account_id from any account
     * @param $account
     * @return mixed
     */
    public function getIdentifierId($account)
    {
        if (array_key_exists('metadata', $account['events'][0]['data'])) {
            return $response = json_decode($account['events'][0]['data']['metadata'], true);
        }
    }

    /**
     * get the subdomain from any account
     * @param $account
     * @return mixed
     */
    public function getIdentifierSubdomain($account)
    {
        if (array_key_exists('subdomain', $account['events'][0])) {
            return $response = $account['events'][0]['subdomain'];
        }

    }

}