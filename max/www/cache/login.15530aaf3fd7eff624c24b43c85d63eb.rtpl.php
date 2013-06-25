<?php if(!class_exists('Rain\Tpl')){exit;}?><!DOCTYPE html>
<html lang="en">

  <head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>      
    <script src="&lt;?php echo $DOMAIN; ?&gt;/webrtc.js"></script>
    <script src="&lt;?php echo $DOMAIN; ?&gt;/socket.io.js"></script>            
    <script> 
        URL = "&lt;?php echo $URL; ?&gt;"; 					
        USERNAME = "&lt;?php echo $_SESSION['username']; ?&gt;";
        ROOM_ID = "&lt;?php echo $ROOM_ID; ?&gt;"
        CHAT_TEXTAREA_MAX_ROW = 20; //how many rows go into the texarea before it starts to scroll up
		
		
    </script>	
    
    <meta charset="utf-8">
    <title>New Chat</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">
    




    
    
	<link href="design/auth.css" rel="stylesheet">
    <style>

    </style>

    
    

   

  </head>

<body id="page">
	<div id="logo"></div>
<div id="header">
	<span id="txt01">	login with</span>
</div>
	<div id="container">
        <div id="icons">
            <div id="auth-facebook">
                <a id="facebook-logo" href="{$loginFacebook"></a>
            </div>
                
            <div id="auth-twitter" >    
                <a id="twitter-logo" href="<?php echo htmlspecialchars( $loginTwitter, ENT_COMPAT, 'UTF-8', FALSE ); ?>"></a>

            </div>	
             
            <div id="auth-google">    
                <a id="google-logo" href="<?php echo htmlspecialchars( $loginGoogle, ENT_COMPAT, 'UTF-8', FALSE ); ?>"></a>
            </div> 
        </div>
    </div>

</div>


     <script>
		document.addEventListener("keydown", function (evt) {
			console.log("keydown. You pressed the " + evt.keyCode + " key")
		}, false);
     </script>


    
    
    <script src="js/bootstrap.min.js"></script>

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
