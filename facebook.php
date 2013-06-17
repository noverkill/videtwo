<?php

require_once('includes/User.php');
require_once('includes/Oauth.php');
require_once('includes/Facebook.php');

session_start();

$app_id = '422772054483470';
$app_secret = '41409559fa996b2e82be677e98f1a9e4';
$callback = 'http://videtwo.com/auth/facebook.php';

$facebook = new Facebook($app_id, $app_secret, $callback);

if($facebook->validateAccessToken()){
    
    $response = $facebook->makeRequest('https://graph.facebook.com/me');
    /*
    print '<pre>';
    print_r($response);
    print '</pre>';
    */
    
    $user = new FacebookUser($response);
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
