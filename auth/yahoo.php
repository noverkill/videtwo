<?php

require_once('../common.php');
require_once('../includes/Oauth.php');
require_once('../includes/Yahoo.php');

$consumer_key = 'dj0yJmk9M1FwNVVWQnVrV2VhJmQ9WVdrOWRuWlZibFIzTXpBbWNHbzlNVE15TnpnM05qRTJNZy0tJnM9Y29uc3VtZXJzZWNyZXQmeD1mNw--';
$consumer_secret = 'f077cd3f27c1b2d91e8c9f4e5a42a14368c48b2a';
$callback = 'http://videtwo.com/auth/yahoo.php';

$yahoo = new Yahoo($consumer_key, $consumer_secret, $callback);

print "/auth/yahoo.com<br>";

print "session:<br><pre>";
print_r($_SESSION);
print "</pre>";
        
//exit;

if($yahoo->validateAccessToken()){
    //print "valid token";
    header('Location: ../yahoo.php');
}

exit;

