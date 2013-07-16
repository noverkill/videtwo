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
	
	$microtime =  microtime(1);
	
	$username = $_SESSION['username']; 
?>

<!DOCTYPE html>

<html>

	<head>
		<meta name="viewport" content="width=device-width, initial-scale=1" />
		<title>videTWo</title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>              
		<script> 
			URL = '<?php echo $URL; ?>'; 					
			USERNAME = '<?php echo $username ?>';
			ROOM_ID = '<?php echo $ROOM_ID; ?>';
		</script>			
		<script src="<?php echo $DOMAIN; ?>/webrtc.js?<?php echo $microtime; ?>"></script>
		<script src="<?php echo $DOMAIN; ?>/socket.io.js?<?php echo $microtime; ?>"></script>   
		<script src="<?php echo $DOMAIN; ?>/jscript.js?<?php echo $microtime; ?>"></script>
		
		<style>
			
			* {
				margin: 0;
				padding: 0;
			}
			
			html, body{
				height: 90% ;
				width:100%;
				padding:0;
				margin:0 auto;
				font-family:Verdana, Geneva, sans-serif;
				font-family:"Segoe UI";
				font-size:16px;
				color:#fff;
				background: transparent url("design/dark/img/bg.png") repeat;	
			}
			
			br {
				line-height: 0;
				clear: both;
			}
			
			a {
				text-align: center;
				margin: 0 0.2em 0 0;
				color: #eee;
				text-decoration: none;
				cursor: pointer;
			}
			
			a:hover {
				text-decoration: underline;
			}
			
			.logo{
				height:40px;
				width:150px;
				background: transparent url("design/images/logos.png") no-repeat;
			}
			
			#leave {
				display: none;
			}
			
			#remoteVideos {
				display: none;
			}
			
			.remoteVideos {
				float: left;
				width: 90%;
				/*border: 1px solid red;*/
			}

			.vframe {
				float: left;
				width: 296px;
				height: 251px;
				background-color: #eee;
				text-align: center;	
				/*border:1px solid white;*/
				padding: 2px;
				margin: 5px 5px 5px 0;
			}

			.vframe > a {
				color: #000;
				line-height: 30px;
				text-decoration: none;
				cursor: pointer;
			}
			
			.vframe > a.me {
				color: #3574C6;
			}			
			
			.vframe > a.curr {
				color: #3574C6;
			}

			.vframe > a > span {
				color: orange;
			}

			.local_video {
				display: block;
				width: 294px;
				height: 221px;
				padding: 0;
				margin: 0;
				background-color: #000;
				border:1px solid #000;
			}
			
			.remote_video {
				display: block;
				width: 294px;
				height: 221px;
				padding: 0;
				margin: 0;
				background-color: #000;
				border:1px solid #000;
			}

			#chat {
				display: none;
			}
			
			#incomingChatMessages {
				background-color: #f5f5f5;
				-webkit-box-shadow: inset 0 2px 3px rgba(0,0,0,0.2);
				box-shadow: inset 0 2px 3px rgba(0,0,0,0.2);
				border-radius: 2px;
				border: solid 1px #ccc;
				padding: 0.4em;
				width:300px;
				min-height:200px;
				/*overflow:auto;*/
				margin: 0;
				font-size: 0.85em;
				outline: none;
				font-family: inherit;;
				box-sizing: border-box;
				color: #000;
			}

			#outgoingChatMessage {
				margin-top: 0.3em;
				width:300px;
				background-color: #f5f5f5;
				-webkit-box-shadow: inset 0 2px 3px rgba(0,0,0,0.2);
				box-shadow: inset 0 2px 3px rgba(0,0,0,0.2);	
				border-radius: 2px;
				border: solid 1px #ccc;
				padding: 0.4em;		
				font-size: 0.85em;
				outline: none;
				font-family: inherit;
				box-sizing: border-box;	
				-webkit-appearance: textfield;			
				-webkit-rtl-ordering: logical;
				-webkit-user-select: text;
				cursor: auto;	
				font: -webkit-small-control;
				color: initial;
				letter-spacing: normal;
				word-spacing: normal;
				text-transform: none;
				text-indent: 0px;
				text-shadow: none;
				display: inline-block;
				text-align: start;	
				-webkit-writing-mode: horizontal-tb;		
			}	
			
		</style>
		
	</head>
	
<body>

	<div class="container">

		<div class="logo"></div>

		<div>Welcome: <?php echo $username; ?></div>
		
		<div id="rooms" class="remoteVideos">Loading Rooms</div>
		
		<br />
		
		<div id="leave"><a onclick="leaveRoom()">Leave room</a></div>
		
		<div id="remoteVideos" class="remoteVideos">
			<div class="vframe" id="local_video_el">
				<video class="local_video" id="local_video" autoplay="autoplay" muted="muted" poster="/design/images/qmf.jpg"></video>
				<a id="ou_<?php echo $username; ?>" class="me"><?php echo $username; ?></a>
			</div>		
		</div>       
		
		<div class="text-chat" id="chat" style="clear:both;">
			<div id="incomingChatMessages"></div>
			<input type="text" id="outgoingChatMessage" />   
		</div>

	</div>
	
	<br />
	
	<div><a href="?logout">Logout</a></div>
	
	<ul id="debug">
	  <li><select id="online-users"><option>Online users</option></select></li>
	  <li><select id="room-users"><option>Users in room</option></select></li>
	</ul>
			
  </body>
  
</html>