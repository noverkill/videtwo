<?php

require_once('common.php');
require_once('includes/User.php');
require_once('includes/Oauth.php');
require_once('includes/Twitter.php');

$consumer_key = 'eki4CS1p75YqJRIFNu7BpQ';
$consumer_secret = 'bHtIJK0blP3RWcOnaQc4u30TVIrDkZZj5Vg7rr897o';
$callback = 'http://videtwo.com/auth/twitter.php';

$twitter = new Twitter($consumer_key, $consumer_secret, $callback);

if($twitter->validateAccessToken()){

    $response = $twitter->makeRequest('https://api.twitter.com/1/account/verify_credentials.json');
    
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
    
	header('Location: ' . $_SESSION['subdomain'] . '/index.php');
}
