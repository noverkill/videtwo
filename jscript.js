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
	
	$('#ou_' + USERNAME.replace(/\./gi, "\\.")).html(USERNAME);
	
	current_chat = 'Room Chat';
	changeChat('cstatus', 'Room chat');                    
	$('#cstatus').click(showRoomChat);
	$('#chat').show();
	webrtc.joinRoom(ROOM_ID, USERNAME);
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
	current_chat_id = new_chat_id.replace(/\./gi, "\\.");
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
		
		//console.log(message);
		
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