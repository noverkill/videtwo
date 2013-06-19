<?php 
    $DOMAIN    = "http://videtwo.com";
    $SUBDOMAIN = "http://videtwo.com";       //"http://max.videtwo.com";
    $PAGE      = "index.php";
    $URL       = $DOMAIN . '/' . $PAGE;

	session_set_cookie_params(0, '/', '.videtwo.com');     
    session_start(); 
    
    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        header("Location: $SUBDOMAIN/index.php");
        exit;
    }
	
	$_SESSION['subdomain'] = $SUBDOMAIN;
	
    $ROOM_ID = isset($_GET['room']) ? $_GET['room'] : 'lobby';
?>
<!DOCTYPE html>
<html>
    <head>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <style>
        *{
            margin: 0;
            padding: 0;
        }

        .clear {
            clear: both;
        } 

        a {
            text-decoration: none;
            color: #4096EE; 
            cursor: pointer;
        }
        
        a:hover {
            color: #356AA0;
        }

        #top_stripe {
            display: block;
            width: 99%;
            max-width: 36em;
            border: 0.1em solid #C3D9FF;
            float: left;           
        }
        
        #top_stripe > *{
            float: left;
            margin: 0 0.5em;
        }
        
        #remoteVideos {
            display: block;
            width: 99%;
            max-width: 36em;
            border: 0.1em solid #C3D9FF;
            float: left;
            margin: 0.5em 0;
        }
        
        .remote_video, .local_video {
            width: 11.5em;           
            height: 8.8em;
            background-color: #CCC;
            border: 0.1em solid #C3D9FF;
            margin: 0.1em; 
            float:left;
        }
        
        #chat {
            display: block;
            width: 99%;
            max-width: 36em;
            border: 0.1em solid #C3D9FF;
            float: left;
            margin: 0.5em 0;
        }
        
        #chat > * {
            float: left;
            width: 99%;
            margin: 0.1em;
        }
        
        #chat > textarea {
            height: 15em;
        }

    </style>  
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

        <?php if(isset($_SESSION['username'])) { ?>
        
            <script src="webrtc.js"></script>
            <script src="socket.io.js"></script>
            
            <script> 

                URL = "<?php echo $URL; ?>"; 
                
                USERNAME = "<?php echo $_SESSION['username']; ?>";
                
                ROOM_ID = "<?php echo $ROOM_ID; ?>"

                CHAT_TEXTAREA_MAX_ROW = 20; //how many rows go into the texarea before it starts to scroll up
                
                var online_users = [];

                var current_chat_id = null;
                var current_chat_text;

                var to_user;
                                                                
                format = function(date){
                    var dd=date.getDate();

                    //if(dd<10)dd='0'+dd;
                    //var mm=date.getMonth()+1;
                    //if(mm<10)mm='0'+mm;
                    //var yyyy=date.getFullYear();
                    
                    var hh = date.getHours();
                    var ms = date.getMinutes();
                    var ss = date.getSeconds();
                    if (ms < 10) ms = "0" + ms;                
                    if (ss < 10) ss = "0" + ss;                
                    
                    //return ''+yyyy+'-'+mm+'-'+dd+'-'+hh+':'+ms+':'+ss;
                    return hh+':'+ms+':'+ss;
                }
                                                
                var webrtc = new WebRTC({
                    localVideoEl: 'remoteVideos',
                    remoteVideosEl: 'remoteVideos',
                    // immediately ask for camera access
                    autoRequestMedia: true
                });
                
                // we have to wait until it's ready
                webrtc.on('readyToCall', function () {
                    current_chat = 'Room Chat';
                    changeChat('cstatus', 'Room chat');                    
                    $('#cstatus').click(showRoomChat);
                    $('#chat').show();
                    webrtc.joinRoom(ROOM_ID);
                });
                                  
                $(document).keypress(function(event) {
                    
                    if(event.which == 13) {
                        
                        event.preventDefault();                                              
                        
                        if(event.target.id == "pch_i-" + to_user) {
                            
                            var now = format(new Date());
                            
                            var sid = webrtc.connection.socket.sessionid;                         
                            
                            console.log(event.target);
                            
                            //console.log("#pch_i-" + to_user);
                        
                            var msg = event.target.value; //$("#pch_i-" + to_user).val();

                            var message = {'sent': now, 'from': USERNAME, 'to': to_user, 'room': ROOM_ID, 'message': msg};
                        
                            webrtc.connection.emit('message', message);
                            
                            console.log(message);
                            
                            var textarea = document.getElementById('pch_t-' + to_user); 
                            
                            var new_data = now + '(to:' + to_user + ')>' + msg;
                            
                            var new_data = now + ' me > ' + msg;
                            
                            var total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
                            
                            console.log(total);
                            
                            if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
                            
                            textarea.value = total.join("\n");
                        }
                    }
                });
                
                function showRoomChat(){
                    
                    var pch_i = document.getElementById('pch_i-' + to_user);
                    var pch_t = document.getElementById('pch_t-' + to_user);
                    
                    if(!!pch_i) {
                        pch_i.style.display = 'none';
                        pch_t.style.display = 'none';
                    }
                                        
                    changeChat('cstatus', 'Room chat');

                    document.getElementById('outgoingChatMessage').style.display = 'block';
                    document.getElementById('incomingChatMessages').style.display = 'block';
                }
                    
                function changeChat(new_chat_id, new_chat_text) {
                    if(current_chat_id !== null) $('#'+current_chat_id).text(current_chat_text);
                    current_chat_id = new_chat_id;
                    current_chat_text = new_chat_text; 
                    $('#'+current_chat_id).text('> ' + current_chat_text);                                       
                }
                
                function showUserChat(new_user){

                    document.getElementById('outgoingChatMessage').style.display = 'none';
                    document.getElementById('incomingChatMessages').style.display = 'none';
                    
                    if(!!to_user) to_user.replace(/\./gi, "\\.");

                    var pch_i = document.getElementById('pch_i-' + to_user);
                    var pch_t = document.getElementById('pch_t-' + to_user);
                    
                    if(!!pch_i) {
                        pch_i.style.display = 'none';
                        pch_t.style.display = 'none';
                    }
                                            
                    to_user = new_user;
                    
                    changeChat('ou_' + to_user, to_user)
                    
                    pch_i = document.getElementById('pch_i-' + to_user);
                    pch_t = document.getElementById('pch_t-' + to_user);
                    
                    if(!!pch_i) {
                        
                        pch_i.style.display = 'block';
                        pch_t.style.display = 'block';
                    
                    } else {

                        var pch = document.getElementById('chat');

			pch_i = document.createElement('input');
                        pch_i.setAttribute('type', 'text');
                        pch_i.setAttribute('id', "pch_i-" + to_user);               
			pch.appendChild(pch_i);
                        
                        var pch_t = document.createElement('Textarea');
                        pch_t.setAttribute('id', "pch_t-" + to_user);
                        pch.appendChild(pch_t);
                    }                         
                }
                                    
                $(function(){
                     
                    $("#create_room").click(function(){
                        console.log("create_room");
                        $.get("create_room.php", function(data, status) {
                            if(status=="success") {
                                //console.log("Data: " + data + "\nStatus: " + status);
                                $("#create_room").text(URL + '?room=' + data);
                                $("#create_room").attr('href', URL + '?room=' + data);
                                $("#create_room").attr('target', '_blank');
                            }
                        });
                    });
                    
                    $('#outgoingChatMessage').keypress(function(event) {
                        if(event.which == 13) {
                            event.preventDefault();
                    
                            var now = format(new Date());
                            
                            var sid = webrtc.connection.socket.sessionid;                         
                            var msg = $('#outgoingChatMessage').val();

                            var message = {'sent': now, 'from': USERNAME, 'room': ROOM_ID, 'message': msg};
                        
                            webrtc.connection.emit('message', message);
                            
                            console.log(message);
                            
                            var textarea = document.getElementById('incomingChatMessages');
                            var new_data = message.sent + ' > ' + message.message;
                            var total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
                            console.log(total);
                            if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
                            textarea.value = total.join("\n");
                        }
                    });

                    webrtc.connection.on('userconnected', function(users) {
                        console.log('userconnected');
                        console.log(users);
                        for (var i in users) {
                            
                            username = users[i];
                            
                            if(username == USERNAME) continue;

                            if(online_users.indexOf(username)>-1) continue;
                            
                            online_users.push(username);
                                                        
                            if(document.getElementById('ou_' + username) !== null) {
                                document.getElementById('ou_' + username).style.color = "green";
                                continue;
                            }

                            var ul = document.getElementById('online_users');
                            var li = document.createElement('li');
                            var a = document.createElement('a');
                            //a.setAttribute('href', '#');
                            a.setAttribute('id', 'ou_' + username);
                            a.setAttribute('onclick', "showUserChat('"+username+"')");
                            a.innerHTML = username;
                            console.log(a);
                            li.appendChild(a);
                            ul.appendChild(li);
                        }
                    });
                    
                    webrtc.connection.on('userdeleted', function(username) {
                        console.log('userdeleted');
                        console.log(username);
                        if(username == USERNAME) return;
                        online_users.splice(online_users.indexOf(username), 1);
                        $('#ou_' + username.replace(/\./gi, "\\.")).css('color', 'grey');
                    });
                    
                    webrtc.connection.on('message', function(message) {
                        
                        var new_data, textarea, total;
                        
                        console.log(message);
                        
                        if(message.message) {
                            
                            new_data = message.sent + ' ' + message.from + ' > ' + message.message;
                            
                            if (message.to) {
                                if (message.to == USERNAME) {
                                    showUserChat(message.from);
                                    console.log('pch_t-' + to_user);
                                    textarea = document.getElementById('pch_t-' + to_user);  
                                }
                            } else if (message.room == ROOM_ID || message.from == 'system') {
                                textarea = document.getElementById('incomingChatMessages');
                            }
                            
                            if(textarea) {
                                total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
                                if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
                                textarea.value = total.join("\n");
                            }
                        }
                    });

                    webrtc.connection.on('connect', function() {
                        webrtc.connection.emit('adduser', USERNAME);
                    });
                                            
                });
            </script>
        <?php } ?>
    </head>
    <body>       
        <?php if(! isset($_SESSION['username'])) { ?>
            <a href="<?php echo $DOMAIN; ?>/facebook.php">Login with Facebook</a> |
            <a href="<?php echo $DOMAIN; ?>/twitter.php">Login with Twitter</a> |        
            <a href="<?php echo $DOMAIN; ?>/google.php">Login with Google</a> |         
            <!--a href="<?php echo $DOMAIN; ?>/yahoo.php">Login with Yahoo</a-->        
        <?php } else { ?>
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
                            $users = scandir('./users/');
                            foreach($users as $user) {
                                if($user != '.' && $user != '..' && $user != '.gitignore') {
                                    print "<li><a id='ou_" . $user . "' onclick='showUserChat(\"$user\")'>$user</a></li>";
                                }
                            }
                        ?>
                    </ul>
                </div>                
            </div>
        <?php } ?>        
    </body>
</html>
