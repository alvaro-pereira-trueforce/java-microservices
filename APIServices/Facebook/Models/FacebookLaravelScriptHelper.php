<?php
namespace APIServices\Facebook\Models;


use Facebook\FacebookApp;
use Facebook\FacebookClient;
use Facebook\Helpers\FacebookSignedRequestFromInputHelper;
use Illuminate\Support\Facades\Log;

class FacebookLaravelScriptHelper extends FacebookSignedRequestFromInputHelper {

    protected $cookie_string;

    /**
     * FacebookLaravelScriptHelper constructor.
     *
     * @param FacebookApp    $app
     * @param FacebookClient $client
     * @param null|string    $graphVersion
     * @param string         $cookie_string
     */
    public function __construct(FacebookApp $app, FacebookClient $client, ?string $graphVersion =
    null, $cookie_string) {
        $this->cookie_string = $cookie_string;
        parent::__construct($app, $client, $graphVersion);
    }

    public function getRawSignedRequest() {
        $cookieName = 'fbsr_' . $this->app->getId();
        list($k, $v) = explode('=', $this->cookie_string);
        $result[ $k ] = $v;
        return $result[$cookieName];
    }
}