<?php

require_once('../common.php');
require_once('../includes/Oauth.php');
require_once('../includes/Google.php');

$app_id = '808251293425.apps.googleusercontent.com';
$app_secret = 'H2YKv7ZL56dh648zbBIHtLrO';
$callback = 'http://videtwo.com/auth/google.php';

$google = new Google($app_id, $app_secret, $callback);

if($google->validateAccessToken()){
    header('Location: ../google.php');
}

exit;
