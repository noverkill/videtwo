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
		<link rel="stylesheet" type="text/css" href="/style.css?<?php echo microtime(1); ?>" media="screen" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>      
		<!--script src="<?php echo $DOMAIN; ?>/webrtc.js"></script-->
		<script src="webrtc.js"></script>
		<script src="<?php echo $DOMAIN; ?>/socket.io.js"></script>            
		<script> 
			URL = "<?php echo $URL; ?>"; 					
			USERNAME = "<?php echo $_SESSION['username']; ?>";
			ROOM_ID = "<?php echo $ROOM_ID; ?>"
			CHAT_TEXTAREA_MAX_ROW = 12; //how many rows go into the textarea before it starts to scroll up
		</script>			
		<script src="jscript.js?<?php echo microtime(1); ?>"></script>
    </head>
    <body>      
		<div id="wrapper">
			<div id="header">
				VIDETWO
			</div>	    
			<div id="tp">
				<div id="top_strip">
					<span>Welcome: <?php print $_SESSION['username']; ?></span> 
					<span id="curr_room">You are in room: <?php echo $ROOM_ID; ?></span>
					<a id="create_room" href="#">Create room</a>
					<a href="?logout">Logout</a>
				</div>    
			</div>  
			<div id="rv">
				<div id="remoteVideos"></div>
			</div>
			<br class="clear" />
			<div id="rvc">
				<div id="chat">
					<input type="text" id="outgoingChatMessage" />
					<textarea id="incomingChatMessages"></textarea>			
					<div style="clear: both;"></div>					
				</div>		
			</div>
			<br class="clear" />
			<div id="bp"></div>			
			<div id="footer">
				&copy; 2013 VIDETWO All rights reserved.
			</div>
		</div>                    
    </body>
</html>
