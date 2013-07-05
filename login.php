<?php 
	include('common.php');	
	
	$_SESSION['subdomain'] = $SUBDOMAIN; 
	
	// template engine configurations
	require "library/Rain/autoload.php";
	
	// namespace
	use Rain\Tpl;
	
	// config
	$config = array(
	"tpl_dir"       => "design/dark/",
	"cache_dir"     => "cache/",
	"debug"         => true, // set to false to improve the speed
	);

	Tpl::configure( $config );

	// create the Tpl object
    $tpl = new Tpl;

    // assign variables
	
	$tpl->assign("DOMAIN", $DOMAIN);
	$tpl->assign("MICROTIME", microtime(1)); 	
	
    $tpl->assign("loginFacebook", $DOMAIN.'/facebook.php' );
    $tpl->assign("loginTwitter", $DOMAIN.'/twitter.php' );
    $tpl->assign("loginGoogle", $DOMAIN.'/google.php' );

    // draw the template
    $tpl->draw( "login" );
	
?>

      
	
