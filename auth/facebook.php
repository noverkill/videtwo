<?php

require_once('../includes/Oauth.php');
require_once('../includes/Facebook.php');

session_start();

$app_id = '422772054483470';
$app_secret = '41409559fa996b2e82be677e98f1a9e4';
$callback = 'http://videtwo.com/auth/facebook.php';

$facebook = new Facebook($app_id, $app_secret, $callback);

if($facebook->validateAccessToken()){
    header('Location: ../facebook.php');
}

exit;
