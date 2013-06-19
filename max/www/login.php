<?php 
	include('common.php');	
	
	$_SESSION['subdomain'] = $SUBDOMAIN; 
?>
<!DOCTYPE html>
<html>
    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" type="text/css" href="/style.css" media="screen" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>
    </head>
    <body>       
		<a href="<?php echo $DOMAIN; ?>/facebook.php">Login with Facebook</a> |
		<a href="<?php echo $DOMAIN; ?>/twitter.php">Login with Twitter</a> |        
		<a href="<?php echo $DOMAIN; ?>/google.php">Login with Google</a> |     
    </body>
</html>