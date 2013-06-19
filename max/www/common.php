<?php

$SITE      = "videtwo.com";
$DOMAIN    = "http://" . $SITE;
$SUBDOMAIN = "http://max.videtwo.com";
$PAGE      = "index.php";
$URL       = $DOMAIN . '/' . $PAGE;

session_set_cookie_params(0, '/', ".$SITE"); 
session_start();
	
?>