<?php 		
	include('common.php');
	
	if(! isset($_SESSION['username'])) {
		header('Location: login.php');
		exit;
	}
	
    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        header("Location: $SUBDOMAIN/index.php");
        exit;
    }
	
    $ROOM_ID = isset($_GET['room']) ? $_GET['room'] : 'lobby';
?>
<!DOCTYPE html>
<html>
    <head>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<link rel="stylesheet" type="text/css" href="/style.css" media="screen" />
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>      
		<script src="<?php echo $DOMAIN; ?>/webrtc.js"></script>
		<script src="<?php echo $DOMAIN; ?>/socket.io.js"></script>            
		<script> 
			URL = "<?php echo $URL; ?>"; 					
			USERNAME = "<?php echo $_SESSION['username']; ?>";
			ROOM_ID = "<?php echo $ROOM_ID; ?>"
			CHAT_TEXTAREA_MAX_ROW = 20; //how many rows go into the texarea before it starts to scroll up
		</script>			
		<script src="jscript.js"></script>
    </head>
    <body>       
		<div id="top_stripe">
			<span>Welcome: <?php print $_SESSION['username']; ?></span> 
			<span id="curr_room">You are in room: <?php echo $ROOM_ID; ?></span>
			<a id="create_room" href="#">Create room</a>
			<a href="?logout">Logout</a>
		</div>
		<br class="clear" />
		<div id="remoteVideos"></div>
		<br class="clear" />
		<div id="bottom_stripe">
			<div>
				<div id="localVideo"></div>
			</div>
			<div id="chat" style="display:none">
				<p>Text Chat</p>
				<input type="text" id="outgoingChatMessage" />
				<textarea id="incomingChatMessages"></textarea>
			</div>
			<br class="clear" />
			<div>
				<p><a id="cstatus">Connecting to chat...</a></p>
				<p>Private Chat with User:</p>
				<ul id="online_users">
					<?php
						$users = scandir('/var/www/users/');
						foreach($users as $user) {
							if($user != '.' && $user != '..' && $user != '.gitignore') {
								print "<li><a id='ou_" . $user . "' onclick='showUserChat(\"$user\")'>$user</a></li>";
							}
						}
					?>
				</ul>
			</div>                
		</div>      
    </body>
</html>
