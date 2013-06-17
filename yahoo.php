<?php

require_once('includes/User.php');
require_once('includes/Oauth.php');
require_once('includes/Yahoo.php');

session_start();

$consumer_key = 'dj0yJmk9M1FwNVVWQnVrV2VhJmQ9WVdrOWRuWlZibFIzTXpBbWNHbzlNVE15TnpnM05qRTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1mNw--';
$consumer_secret = 'f077cd3f27c1b2d91e8c9f4e5a42a14368c48b2a';
$callback = 'http://videtwo.com/auth/yahoo.php';

$yahoo = new Yahoo($consumer_key, $consumer_secret, $callback);

if($yahoo->validateAccessToken()){
/*
    $response = $yahoo->makeRequest('https://api.twitter.com/1/account/verify_credentials.json');
    
    $user = new TwitterUser($response);
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
*/
}
