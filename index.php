<?php 		
	include('common.php');
	
	if(! isset($_SESSION['username'])) {
		header('Location: login.php');
		exit;
	}
	
    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        header("Location: $SUBDOMAIN/login.php");
        exit;
    }
	
    $ROOM_ID = isset($_GET['room']) ? $_GET['room'] : 'lobby';
	
	
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
    $tpl->assign("URL", $URL);
    $tpl->assign("site_title", "New Chat" );
	$tpl->assign("user_nick", $_SESSION['username']);
	$tpl->assign("room_name", $ROOM_ID); 

    // draw the template
    $tpl->draw( "index" );

?>