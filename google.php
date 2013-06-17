<?php

require_once('includes/User.php');
require_once('includes/Oauth.php');
require_once('includes/Google.php');

session_start();

$app_id = '808251293425.apps.googleusercontent.com';
$app_secret = 'H2YKv7ZL56dh648zbBIHtLrO';
$callback = 'http://videtwo.com/auth/google.php';

$google = new Google($app_id, $app_secret, $callback);

//print_r($google);

if($google->validateAccessToken()){
    
    //docs: https://developers.google.com/accounts/docs/OAuth2Login#offlineaccess
    //docs: http://stackoverflow.com/questions/10664868/where-can-i-find-a-list-of-scopes-for-googles-oauth-2-0-api
    //$response = $google->makeRequest('https://www.googleapis.com/auth/userinfo.profile');
    //$response = $google->makeRequest('https://www.googleapis.com/auth/plus.me');
    //$response = $google->makeRequest('https://www.googleapis.com/plus/v1/people/me');    
    $response = $google->makeRequest('https://www.googleapis.com/oauth2/v3/userinfo');
    
    /*
    print 'response:<pre>';
    print_r($response);
    print '</pre>';
    */
    
    $user = new GoogleUser($response);
    
    $ufile = "users/".$user->id;
    //print "ufile: $ufile<br>";
    
    if(! is_file($ufile)){
        file_put_contents($ufile, (serialize($user)));
        //print "user not exists<br>";
    } else {
        //print "user exists<br>";
    }
    
    $_SESSION['username'] = $user->username;
    
    header('Location: index.php');
}
