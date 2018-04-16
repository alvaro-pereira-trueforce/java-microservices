<?php

namespace APIServices\Instagram\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use MetzWeb\Instagram\Instagram;


class InstagramController extends Controller
{
    protected $instagram;
    public function __construct()
    {
        //Jaciel
        $this->instagram = new Instagram(array(
            'apiKey'      => 'c133bd0821124643a3a0b5fbe77ee729',
            'apiSecret'   => '308973f7f4944f699a223c74ba687979',
            'apiCallback' => 'https://twitter.com/soysantizeta'
        ));
        /*
        $this->instagram = new Instagram(array(
            'apiKey'      => '6b5cd72dae664ba9861d7d41fb3ff4e0',
            'apiSecret'   => '86460e3edc85471d96b1c686a53fd6d6',
            'apiCallback' => 'https://twitter.com/soysantizeta'
        ));
        */
    }
    public function getOAuhtToken(){
        //echo "<a href='{$this->instagram->getLoginUrl(array('basic','likes','comments','relationships'))}'>Login with Instagram</a>";
        //$code = $_GET['code'];

        // $data =  $this->instagram->getOAuthToken('e2013a4f547549ad847c38f0efee1cfd');

       // echo 'Your username is: ' . $data->user->username;
        $this->instagram->setAccessToken('7508601214.c133bd0.051c95263aed436cbafd5c55b0626d57');

        // get all user likes
       // $likes = $this->instagram->getUserLikes();


        $user = $this->instagram->getUser();


        //$user = $this->instagram->getUserMedia(7508601214,0); funciona con tre
        //$user = $this->instagram->getMediaComments('1758602735810303054_7508601214');

        //$user = $this->instagram->getUserMedia();
        //$user = $this->instagram->getUserMedia();
        //-17.404655, -66.162072
        //$user = $this->instagram->searchMedia(-17.404655,-66.162072,1000000);da vacio
       // $user = $this->instagram->searchUser('jaciel');
        // take a look at the API response
        //echo '<pre>';
      //  print_r($user);
       // echo '<pre>';
        return response()->json($user,200);
    }

    //
}
