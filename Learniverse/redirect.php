<?php
require_once 'vendor/autoload.php';

// init configuration
$clientID = '925397460999-29ntg8t21mov1vr2545uo0hpmiebhhjh.apps.googleusercontent.com';
$clientSecret = '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';
$redirectUri = 'http://localhost/newcheck_2/redirect.php';

// create Client Request to access Google API
$client = new Google_Client();
$client->setClientId($clientID);
$client->setClientSecret($clientSecret);
$client->setRedirectUri($redirectUri);
$client->addScope("email");
$client->addScope("profile");

// authenticate code from Google OAuth Flow
if (isset($_GET['code'])) {
    $token = $client->fetchAccessTokenWithAuthCode($_GET['code']);
//    $client->setAccessToken($token['access_token']);

    // get profile info
    $google_oauth = new Google_Service_Oauth2($client);
    $google_account_info = $google_oauth->userinfo->get();
    $email =  $google_account_info->email;
    $name =  $google_account_info->name;
    header('Location: index.html');
    // now you can use this profile info to create account in your website and make user logged in.
} else {
    echo "<a id='google' href='".$client->createAuthUrl()."'><img src='google.png'><p>Sign Up with Google<p/></a>";
}
?>