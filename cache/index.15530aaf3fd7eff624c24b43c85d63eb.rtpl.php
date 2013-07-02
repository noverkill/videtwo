<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="en">

  <head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>      
    <script src="<?php echo htmlspecialchars( $DOMAIN, ENT_COMPAT, 'UTF-8', FALSE ); ?>/webrtc.js?<?php echo htmlspecialchars( $MICROTIME, ENT_COMPAT, 'UTF-8', FALSE ); ?>"></script>
    <script src="<?php echo htmlspecialchars( $DOMAIN, ENT_COMPAT, 'UTF-8', FALSE ); ?>/socket.io.js"></script>            
    <script> 
        URL = "<?php echo htmlspecialchars( $URL, ENT_COMPAT, 'UTF-8', FALSE ); ?>"; 					
        USERNAME = "<?php echo htmlspecialchars( $user_nick, ENT_COMPAT, 'UTF-8', FALSE ); ?>";
        ROOM_ID = "<?php echo htmlspecialchars( $room_name, ENT_COMPAT, 'UTF-8', FALSE ); ?>"
        CHAT_TEXTAREA_MAX_ROW = 20; //how many rows go into the texarea before it starts to scroll up		
    </script>	
	<script>
		var socket = io.connect('http://videtwo.com:8080');

		// on connection to server, ask for user's name with an anonymous callback
		socket.on('connect', function(){
			// call the server-side function 'adduser' and send one parameter (value of prompt)
			socket.emit('adduser', USERNAME /*prompt("What's your name?")*/);
		});

		// listener, whenever the server emits 'updatechat', this updates the chat body
		socket.on('updatechat', function (username, data) {
			$('#conversation').append('<b>'+username + ':</b> ' + data + '<br>');
		});

		// listener, whenever the server emits 'updateusers', this updates the username list
		socket.on('updateusers', function(data) {
			$('#users').empty();
			$.each(data, function(key, value) {
				$('#users').append('<div>' + key + '</div>');
			});
		});

		// on load of page
		$(function(){
			// when the client clicks SEND
			$('#datasend').click( function() {
				var message = $('#data').val();
				$('#data').val('');
				// tell server to execute 'sendchat' and send along one parameter
				socket.emit('sendchat', message);
			});

			// when the client hits ENTER on their keyboard
			$('#data').keypress(function(e) {
				if(e.which == 13) {
					$(this).blur();
					$('#datasend').focus().click();
				}
			});
		});
	</script>	
	<script src="<?php echo htmlspecialchars( $DOMAIN, ENT_COMPAT, 'UTF-8', FALSE ); ?>/jscript.js?<?php echo htmlspecialchars( $MICROTIME, ENT_COMPAT, 'UTF-8', FALSE ); ?>"></script>
    
    <meta charset="utf-8">
    <title>New Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    




    
    
	<link href="design/dark/css/dark.css?<?php echo htmlspecialchars( $MICROTIME, ENT_COMPAT, 'UTF-8', FALSE ); ?>" rel="stylesheet">
    <style>
#remoteVideos {
	display: block;
	width: 100%;
	max-width: 36.7em;
	margin-left: auto;
	margin-right:auto;
}

.vframe {
	float:left;
	width: 17.25em; /*11.5em;*/           
	height: 15em; /*10.4em;*/
    background-color: #eee;
	-moz-box-shadow: 0 0 5px #fff;
	-webkit-box-shadow: 0 0 5px #fff;
	box-shadow: 0 0 5px #ccc;
	margin: 0.25em;
	padding: 0.11em;	
	text-align: center;	
}

.vframe > a {
	color: #000;
	cursor: pointer;
}
.vframe > a.me {
	color: #3574C6;
}

.vframe > a > span {
	color: orange;
}

.messaged {
	padding: 20px !important;
	background: url('orange_dot.gif') center right no-repeat !important;
}

.selected {
	text-decoration: underline !important;
}

.remote_video, .local_video {
	width: 17.25em;           
	height: 13.05em;
}

#chat {
	display: block;
	width: 100%;
	max-width: 36.3em;
	margin-left: auto;
	margin-right:auto;
}

#chat > input {
	width: 100%;
	float: left;
	margin: 0.2em 0;
	font-size: 1.5em;
}	

#chat > textarea {
	width: 100%;
	height: 15em;
	float: left;
	margin: 0.2em 0;	
	background-color: #CCC;		
	border:0;			
}

    </style>

    
    

   

  </head>

  <body id="page">






<div class="container">

<div class="navbar">
<div class="logo">
<h1><a id="brand" href="#"><?php echo htmlspecialchars( $site_title, ENT_COMPAT, 'UTF-8', FALSE ); ?></a></h1>
</div>
    
	<div class="navlinks">

			<ul class="nav">
              <li ><span class="navtext">Welcome:</span><a class="navtext" href="{url_profile}"><?php echo htmlspecialchars( $user_nick, ENT_COMPAT, 'UTF-8', FALSE ); ?></a></li>
              <li><span class="navtext">|</span> </li>
              <li><a class="navtext" href="?logout">Logout</a></li>
            </ul>
  
            <ul class="nav">
              <li class="active"><a href="#" class="navtext">About</a></li>
              <span class="navtext">|</span> 
              <li><a href="#about" class="navtext">FAQ</a></li>
              <span class="navtext">|</span> 
              <li><a href="#contact" class="navtext">Contact</a></li>
            </ul>
	</div>

</div>
		<div class="rooms-o">
        	<div class="rooms-i">
                <span>rooms</span>
    
                <select id="dd-rooms">
                  <option value="Lobby">Lobby</option>
                  <option value="Flame">Flame</option>
                  <option value="Debugging">Debugging</option>
                  <option value="Data mining">Data mining</option>
                </select>
            </div>    
        </div>
         <div class="controls">
            <p>
                <button id="start-video">Start video</button>
                <button id="view-fullscreen">Fullscreen</button>
                <button id="cancel-fullscreen">Cancel fullscreen</button>
            </p>
        </div>
        
        <div class="users-list">
       	 <p >users</p>
        </div>
        
       
        <div class="text-chat">

			<b>Online users</b>
			<div id="users"></div>
	
			
			
			<br />
			<br />
			
			<div id="conversation"></div>
			<input id="data" style="width:200px;" />
			<input type="button" id="datasend" value="send" />			
			
			
		</div>
		
        <div class="video-chat ">
			<h1 class="big">video</h1>
			<div id="remoteVideos"></div>
		</div>

</div>


     <script>
		document.addEventListener("keydown", function (evt) {
			console.log("keydown. You pressed the " + evt.keyCode + " key")
		}, false);
     </script>


    
    
    <script src="./design/dark/js/bootstrap.min.js"></script>

    <script>
		var page = document.getElementById('page'),
		ua = navigator.userAgent,
		iphone = ~ua.indexOf('iPhone') || ~ua.indexOf('iPod'),
		ipad = ~ua.indexOf('iPad'),
		ios = iphone || ipad,
		// Detect if this is running as a fullscreen app from the homescreen
		fullscreen = window.navigator.standalone,
		android = ~ua.indexOf('Android'),
		lastWidth = 0;
		 
		if (android) {
		// Android's browser adds the scroll position to the innerHeight, just to
		// make this really fucking difficult. Thus, once we are scrolled, the
		// page height value needs to be corrected in case the page is loaded
		// when already scrolled down. The pageYOffset is of no use, since it always
		// returns 0 while the address bar is displayed.
		window.onscroll = function() {
		page.style.height = window.innerHeight + 'px'
		}
		}
		var setupScroll = window.onload = function() {
		// Start out by adding the height of the location bar to the width, so that
		// we can scroll past it
		if (ios) {
		// iOS reliably returns the innerWindow size for documentElement.clientHeight
		// but window.innerHeight is sometimes the wrong value after rotating
		// the orientation
		var height = document.documentElement.clientHeight;
		// Only add extra padding to the height on iphone / ipod, since the ipad
		// browser doesn't scroll off the location bar.
		if (iphone && !fullscreen) height += 60;
		page.style.height = height + 'px';
		} else if (android) {
		// The stock Android browser has a location bar height of 56 pixels, but
		// this very likely could be broken in other Android browsers.
		page.style.height = (window.innerHeight + 56) + 'px'
		}
		// Scroll after a timeout, since iOS will scroll to the top of the page
		// after it fires the onload event
		setTimeout(scrollTo, 0, 0, 1);
		};
		(window.onresize = function() {
		var pageWidth = page.offsetWidth;
		// Android doesn't support orientation change, so check for when the width
		// changes to figure out when the orientation changes
		if (lastWidth == pageWidth) return;
		lastWidth = pageWidth;
		setupScroll();
		})();


</script>
    
	
	<script src="../assets/js/holder/holder.js"></script>
    
    <script src="js/base.js"></script>
  </body>
</html>
