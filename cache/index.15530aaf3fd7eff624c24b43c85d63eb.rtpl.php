<?php if(!class_exists('Rain\Tpl')){exit;}?><body id="page">


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
            	<div class="styled">
                        <select id="rooms">						
                          
                        </select>
                </div>    
            </div>    
        </div>
        
        <div class="controls-o">
            <div class="controls-i">
                <input type="button" id="view-fullscreen" value="Fullscreen">
                <input type="button" id="cancel-fullscreen" value="Cancel Fullscreen">
            </div>
        </div>
        

        <input type="button" id="start-video" value="Start video">        
     
		<div class="users-list" id="users" style="color:#fff;float:left"></div>
		
		

		
			
        <div class="text-chat" id="chat">
			<textarea id="incomingChatMessages"></textarea>
			<input type="text" id="outgoingChatMessage" />   
        </div>
        
        <div class="video-chat ">
        	<div id="remoteVideos"></div>       
        </div>

</div>


     <script>
		document.addEventListener("keydown", function (evt) {
			console.log("keydown. You pressed the " + evt.keyCode + " key")
		}, false);
     </script>


    
    
    <script src="design/dark/js/bootstrap.min.js"></script>

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
    
    <script src="design/dark/js/base.js"></script>
  </body>
</html>
