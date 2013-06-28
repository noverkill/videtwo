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

//escape usernames before use with jQuery id selector
//(because the dot in the username create problems)
u = function (n) {
	return n.replace(/\./gi, "\\.");
}

var webrtc = new WebRTC({
	localVideoEl: 'remoteVideos',
	remoteVideosEl: 'remoteVideos',
	// immediately ask for camera access
	autoRequestMedia: true
});

// we have to wait until it's ready
webrtc.on('readyToCall', function () {
		 
	$('.me').html(USERNAME);
	
	$('#chat').show();
	
	showRoomChat();
	
	webrtc.joinRoom(ROOM_ID, USERNAME);
});
				  
$(document).keypress(function(event) {
	
	if(event.which == 13) {
		
		console.log('keypress');
		
		event.preventDefault();                                              
		
		//console.log(event.target.id);
		
		var tid = event.target.id.split('-');

		console.log(tid);
		
		//console.log(tid[0]);
		
		if(tid[0] == 'pch_i') {
			
			var to_user = tid[1];
			
			//console.log(to_user);
			
			var now = format(new Date());
			
			var sid = webrtc.connection.socket.sessionid;                         
			
			//console.log(event.target);
		
			var msg = event.target.value;
			
			$(event.target).val('');

			var message = {'sent': now, 'from': USERNAME, 'to': to_user, 'room': ROOM_ID, 'message': msg};
		
			webrtc.connection.emit('message', message);
			
			console.log(message);
			
			var textarea = getUserChatTextArea(to_user); 
			
			console.log(textarea);
			
			var new_data = now + '(to:' + to_user + ')>' + msg;
			
			var new_data = now + ' me > ' + msg;
			
			var total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
			
			console.log(total);
			
			if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
			
			textarea.value = total.join("\n");
			
		} else if ( tid[0] == 'outgoingChatMessage') {
		
			event.preventDefault();
	
			var now = format(new Date());
			
			var sid = webrtc.connection.socket.sessionid;    
			
			var msg = $('#outgoingChatMessage').val();
			
			$('#outgoingChatMessage').val('');

			var message = {'sent': now, 'from': USERNAME, 'room': ROOM_ID, 'message': msg};
		
			webrtc.connection.emit('message', message);
			
			//console.log(message);
			
			var textarea = document.getElementById('incomingChatMessages');
			var new_data = message.sent + ' > ' + message.message;
			var total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
			console.log(total);
			if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
			textarea.value = total.join("\n");
		}
	}
});

function showRoomChat(){
	
	$('#chat').children().hide();

	document.getElementById('outgoingChatMessage').style.display = 'block';
	document.getElementById('incomingChatMessages').style.display = 'block';	
	
	$('.vframe>a').removeClass('selected');
	
	$('.me').addClass('selected');

	$('.me').removeClass('messaged');	
		
}
	
function showUserChat (user){
			
	$('#chat').children().hide();

	var pch_t = getUserChatTextArea (user);
	
	var pch_i = document.getElementById('pch_i-' + user);
	
	pch_i.style.display = 'block';
	pch_t.style.display = 'block';

	$('.vframe>a').removeClass('selected');
	
	$('#ou_' + u(user)).addClass('selected');

	$('#ou_' + u(user)).removeClass('messaged');		
}

function getUserChatTextArea (user) {

	console.log('getUserChatTextArea');
	
	var pch_t = document.getElementById('pch_t-' + user);
	
	console.log(pch_t);
	
	if(!!pch_t) {

	} else {
		  
		var pch = document.getElementById('chat');
	
		var pch_i = document.createElement('input');
		pch_i.setAttribute('type', 'text');
		pch_i.setAttribute('id', "pch_i-" + user);   
		pch_i.style.display = 'none';
		pch.appendChild(pch_i);
		
		pch_t = document.createElement('Textarea');
		pch_t.setAttribute('id', "pch_t-" + user);
		pch_t.style.display = 'none';
		pch.appendChild(pch_t);
	} 
	
	return pch_t;
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
		$('#ou_' + u(username)).css('color', 'grey');
	});
	
	webrtc.connection.on('message', function(message) {
		
		var new_data, textarea, total;
		
		//console.log('message');
		//console.log(message);
		
		if(message.message) {
			
			new_data = message.sent + ' ' + message.from + ' > ' + message.message;
			
			if (message.to) {
				if (message.to == USERNAME) {
					
					$('#ou_' + u(message.from) + ':not(.messaged,.selected)').addClass('messaged');
					
					textarea = getUserChatTextArea(message.from); 
				}
			} else if (message.room == ROOM_ID || message.from == 'system') {
				textarea = document.getElementById('incomingChatMessages');

				$('.me:not(.messaged,.selected)').addClass('messaged');					
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