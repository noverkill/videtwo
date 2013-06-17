<?php 
    $DOMAIN = "http://videtwo.com";
    $PAGE   = "index.php";
    $URL    = $DOMAIN . '/' . $PAGE;
    
    session_start(); 
    
    if(isset($_GET['logout'])){
        session_unset();
        session_destroy();
        header('Location: index.php');
        exit;
    }

    $ROOM_ID = isset($_GET['room']) ? $_GET['room'] : 'lobby';
?>
<!DOCTYPE html>
<html>
    <head>
        <style>
            *{
                margin: 0;
                padding: 0;
            }

            #top_stripe {
                margin: 10px;
            }
            
            #localVideo {
                margin: 10px;
                width: 150px;
                height: 100px;
                border: 1px solid red;
            }

            #remoteVideos {
                margin: 10px;
                width: 50%;
                height: 200px;
                border: 1px solid red;
                float: left;
            }
            
            #new_room {
                margin: 10px 0;
            }
            
            #new_room > a {
                margin-left: 10px;
            }
            
            #cstatus {
                margin: 10px;
            }
            
            #chat {
                margin: 10px;
            }
            
            #chat > * {
                margin-bottom: 10px;
            }
            
            #outgoingChatMessage {
                display:block;
                width: 320px;
            }

            #incomingChatMessages {
                width:320px;
                height:320px;
            }

            #bottom_stripe {
                height:400px;
            }

            #bottom_stripe > div {
                float: left;
                margin: 5px 20px;
                height:385px;
            }            
            
            #user_chat {
                margin: 10px;
            }
            
            #user_chat > * {
                margin-bottom: 10px;
            }
            
            #outgoingUserChatMessage {
                display:block;
                width: 320px;
            }

            #incomingUserChatMessages {
                width:320px;
                height:320px;
            }  

            #online_users > li > a {
                color:grey;                
            }
            
            #online_users {
                list-style-type:none;                
            }

        </style>
        <script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js"></script>

        <?php if(isset($_SESSION['username'])) { ?>
        
            <script src="simplewebrtc.js"></script>
            <script src="socket.io.js"></script>
            
            <script> 

                URL = "<?php echo $URL; ?>"; 
                
                USERNAME = "<?php echo $_SESSION['username']; ?>";
                
                ROOM_ID = "<?php echo $ROOM_ID; ?>";
                            
                LOCAL_VIDEO_DEFAULT_WIDTH = '150px';
                LOCAL_VIDEO_DEFAULT_HEIGHT = '100px';
                
                REMOTE_VIDEO_DEFAULT_WIDTH = '300px';
                REMOTE_VIDEO_DEFAULT_HEIGHT = '200px';

                CHAT_TEXTAREA_MAX_ROW = 20;
                
                var online_users = [];
                                
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
                    // the id of (or actual element) to hold "our" video
                    localVideoEl: 'localVideo',

                    // the id of or actual element that will hold remote videos
                    remoteVideosEl: 'remoteVideos',

                    // immediately ask for camera access
                    autoRequestMedia: true
                });
                
                // we have to wait until it's ready
                webrtc.on('readyToCall', function () {
                    $('#cstatus').text('Room chat');
                    ////$('#cstatus').hide();
                    $('#chat').show();
                    webrtc.joinRoom(ROOM_ID);
                });

                var to_user;
                                  
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

                    $("#cstatus").text("Room chat");

                    document.getElementById('outgoingChatMessage').style.display = 'block';
                    document.getElementById('incomingChatMessages').style.display = 'block';
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
                                            
                    $("#cstatus").text("Private chat with " + to_user);
                    
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
                        pch_i.style.display = 'block';
                        pch_i.style.width = '320px';                
                        pch.appendChild(pch_i);
                        
                        var pch_t = document.createElement('Textarea');
                        pch_t.setAttribute('id', "pch_t-" + to_user);
                        pch_t.style.width = '320px';
                        pch_t.style.height = '320px';
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
                            a.setAttribute('href', '#');
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
        <!--h4>Video chat</h4-->
        
        <?php if(! isset($_SESSION['username'])) { ?>
            <a href="/facebook.php">Login with Facebook</a> |
            <a href="/twitter.php">Login with Twitter</a> |        
            <a href="/google.php">Login with Google</a> |         
            <!--a href="/yahoo.php">Login with Yahoo</a-->        
        <?php } else { ?>
            <p id="top_stripe">
                Welcome: <?php print $_SESSION['username']; ?> | 
                <span id="curr_room">You are in room: <?php echo $ROOM_ID; ?></span> |
                <a href="?logout">Logout</a>
            </p>
            <div id="remoteVideos"></div>
            <br style="clear:both" />
            <div id="bottom_stripe">
                <!--div>
                    <p id="cstatus">Connecting to chat... Please wait...</p> 
                    <div id="chat" style="display:none">
                        <input type="text" id="outgoingChatMessage" />
                        <textarea id="incomingChatMessages"></textarea>
                    </div>
                </div-->
                <div>
                    <div id="localVideo"></div>
                </div>
                <div>
                    <p id="new_room"><a id="create_room" href="#">Create room</a></p>
                    <p><a href="#" onclick="showRoomChat()">Room chat</a></p>
                    <p>Users</p>
                    <ul id="online_users">
                        <?php
                            $users = scandir('./users/');
                            foreach($users as $user) {
                                if($user != '.' && $user != '..') {
                                    print "<li><a href='#' id='ou_" . $user . "' onclick='showUserChat(\"$user\")'>$user</a></li>";
                                }
                            }
                        ?>
                    </ul>
                </div>
                <div>
                    <!--p>Private chat with: <span id="sel_user"></span></p> 
                    <div id="private_chat"> </div-->   
                    <p id="cstatus">Connecting to chat... Please wait...</p> 
                    <div id="chat" style="display:none">
                        <input type="text" id="outgoingChatMessage" />
                        <textarea id="incomingChatMessages"></textarea>
                    </div>
                </div>
            </div>
            <br style="clear:both" />
        <?php } ?>        
    </body>
</html>
