<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

$SITE      = "videtwo.com";
$DOMAIN    = "http://" . $SITE;
$SUBDOMAIN = "http://max." . $SITE;
$PAGE      = "index.php";
$URL       = $DOMAIN . '/' . $PAGE;

session_set_cookie_params(0, '/', ".$SITE"); 
session_start();

?>