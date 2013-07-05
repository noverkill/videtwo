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
	
?>

<!DOCTYPE html>
<html>
    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
        <title>New Chat</title>
		<link rel="stylesheet" type="text/css" href="/style.css?<?php echo microtime(1); ?>" media="screen" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>      
		<script src="<?php echo $DOMAIN; ?>/webrtc.js?<?php echo microtime(1); ?>"></script>
		<script src="<?php echo $DOMAIN; ?>/socket.io.js"></script>      
        <link href="design/dark/css/dark.css?<?php echo microtime(1); ?>" rel="stylesheet">      
		<script> 
			URL = "<?php echo $URL; ?>"; 					
			USERNAME = "<?php echo $_SESSION['username']; ?>";
			ROOM_ID = "<?php echo $ROOM_ID; ?>"
			CHAT_TEXTAREA_MAX_ROW = 12; //how many rows go into the textarea before it starts to scroll up
		</script>			
		<script src="jscript.js?<?php echo microtime(1); ?>"></script>
        <!–[if IE 7 ]> <html lang="en" class="ie7″> <![endif]–>
        <!–[if IE 8 ]> <html lang="en" class="ie8″> <![endif]–>
        <!–[if IE 9 ]> <html lang="en" class="ie9″> <![endif]–>
        <!–[if (gt IE 9)|!(IE)]><!–> <html lang="en"> <!–<![endif]–> 
    </head>

<?php
	
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
	$tpl->assign("MICROTIME", microtime(1)); 	
    $tpl->assign("site_title", "New Chat" );
	$tpl->assign("user_nick", $_SESSION['username']);
	$tpl->assign("room_name", $ROOM_ID); 

	$users = scandir('/var/www/users/');
	foreach($users as $user) {
		if($user != '.' && $user != '..' && $user != '.gitignore') {

			$tpl ->assign("userslist", $user);
		}
	}
	
    // draw the template
    $tpl->draw( "index" );
?>