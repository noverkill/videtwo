<?php

require_once('../common.php');
require_once('../includes/Oauth.php');
require_once('../includes/Twitter.php');

$consumer_key = 'eki4CS1p75YqJRIFNu7BpQ';
$consumer_secret = 'bHtIJK0blP3RWcOnaQc4u30TVIrDkZZj5Vg7rr897o';
$callback = 'http://videtwo.com/auth/twitter.php';

$twitter = new Twitter($consumer_key, $consumer_secret, $callback);

/*
print "/auth/twitter.com<br>";

print "session:<br><pre>";
print_r($_SESSION);
print "</pre>";
        
exit;
*/

if($twitter->validateAccessToken()){
    header('Location: ../twitter.php');
}

exit;

