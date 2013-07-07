
var usernames = [];
var usercolors = [];
		
var room_users = [];
		
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
//(because the dot in the username creates problems)
u = function (n) {
	return n.replace(/\./gi, "\\.");
}
	
var socket = io.connect('http://videtwo.com:8080');

// on connection to server, ask for user's name with an anonymous callback
socket.on('connect', function(){
	// call the server-side function 'adduser' and send one parameter (value of prompt)
	console.log('adduser ' + USERNAME);
	socket.emit('adduser', USERNAME /*prompt("What's your name?")*/);
});

// listener, whenever the server emits 'updatechat', this updates the chat body
socket.on('updatechat', function (username, data, history) {
	
	/*
	console.log('updatechat');
	console.log(username);
	console.log(data);
	console.log(history);
	*/
	
	if(history && history.length) {
	    $('#incomingChatMessages').empty();
		$.each(history, function(key, value) {
			if(value.author != 'SERVER') {
				addToChat(value.author, value);
			}
		});
	}
	
	if(username != 'SERVER') addToChat(username, data);
});

// listener, whenever the server emits 'updateusers', this updates the username list
socket.on('updateusers', function(unames, ucolors) {
	usernames = unames;	
	usercolors = ucolors;
	
	$('#users').empty();
	
	$('#users').append('<span style="color: white">Logged in users: </span>');
	
	for(var i in usernames) {
		$('#users').append('<span class="user" style="margin: 0 5px; color:' + usercolors[i] + ';">' + usernames[i] +  '<span>');
	}
});

socket.on('room_users', function(rusers, room) {
	console.log('room_users');

	room_users = rusers;
	
	console.log(room_users);

	$('#room_users').empty();
	
	$('#room_users').append('<span style="color: white">Users in room ' + room + ': </span>');
	
	for(var i in room_users) {
		$('#room_users').append('<span class="user" style="margin: 0 5px; color:' + usercolors[room_users[i]] + ';">' + room_users[i] +  '<span>');
	}
});

// listener, whenever the server emits 'updaterooms', this updates the room the client is in
socket.on('updaterooms', function(rooms, current_room) {
	$('#rooms').empty();
	$('#rooms').append('<option>Switch room</option>');
	$.each(rooms, function(key, value) {
		$('#rooms').append('<option>' + value + '</option>');
		/*
		if(value == current_room){
			$('#rooms').append('<option>' + value + '</option>');
		}
		else {
			$('#rooms').append('<div><a href="#" onclick="switchRoom(\''+value+'\')">' + value + '</a></div>');
		}
		*/
	});
});

function switchRoom(room){
	socket.emit('switchRoom', room);
	$('#incomingChatMessages').empty();
}

function addToChat(username, data) {
	
	var dt = new Date(data.time);
	
	var incomingChatMessages = $('#incomingChatMessages');
	
	//incomingChatMessages.prepend('<p><span style="color:' + usercolors[username] + '">' + data.text + '</p>');

	incomingChatMessages.prepend('<p><span style="color:' + usercolors[username] + '">' + username + '</span> @ ' +
             + (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
             + (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes())
             + ': ' + data.text + '</p>');
			 
}

var webrtc = new WebRTC({
	localVideoEl: 'remoteVideos',
	remoteVideosEl: 'remoteVideos',
	// immediately ask for camera access
	//autoRequestMedia: false
});

// we have to wait until it's ready
webrtc.on('readyToCall', function () {
		 
	$('.me').html(USERNAME);
	
	//showRoomChat();
	
	webrtc.joinRoom(ROOM_ID, USERNAME);
});

/*	
$(document).keypress(function(event) {
	
	if(event.which == 13) {
				
		event.preventDefault();                                              
		
		var tid = event.target.id.split('-');
		
		if(tid[0] == 'pch_i') {
			
			var to_user = tid[1];
			
			var now = format(new Date());
			
			var sid = webrtc.connection.socket.sessionid;                         
		
			var msg = event.target.value;
			
			$(event.target).val('');

			var message = {'sent': now, 'from': USERNAME, 'to': to_user, 'room': ROOM_ID, 'message': msg};
		
			webrtc.connection.emit('message', message);
			
			var textarea = getUserChatTextArea(to_user); 
			
			var new_data = now + '(to:' + to_user + ')>' + msg;
			
			var new_data = now + ' me > ' + msg;
			
			var total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
			
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
			
			console.log(message);
			
			var textarea = document.getElementById('incomingChatMessages');
			var new_data = message.sent + ' > ' + message.message;
			var total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
			if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
			textarea.value = total.join("\n");
		}
	}
});
*/

/*
function showRoomChat(){
	
	$('#chat').children().hide();

	document.getElementById('outgoingChatMessage').style.display = 'block';
	document.getElementById('incomingChatMessages').style.display = 'block';	
	
	$('.vframe>a').removeClass('selected');
	
	$('.me').addClass('selected');

	$('.me').removeClass('messaged');	
		
}
*/

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

	//console.log('getUserChatTextArea');
	
	var pch_t = document.getElementById('pch_t-' + user);
	
	//console.log(pch_t);
	
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

	$('#rooms').change(function(e) {
		switchRoom(this.options[this.selectedIndex].value);
	});
	
	$('#outgoingChatMessage').keypress(function(e) {
		if(e.which == 13) {
			//$(this).blur();
			
			var obj = {
				time: (new Date()).getTime(),
				text: $(this).val()
			};

			$(this).val('');
			
			socket.emit('sendchat', obj);
		}
	});
	
	$('#start-video').click(function(){
		//console.log('start-video');
		$('#start-video').attr("disabled", true);
		webrtc.startLocalVideo();
	})
	 
	$("#create_room").click(function(){
		//console.log("create_room");
		$.get("create_room.php", function(data, status) {
			if(status=="success") {
				//console.log("Data: " + data + "\nStatus: " + status);
				$("#create_room").text(URL + '?room=' + data);
				$("#create_room").attr('href', URL + '?room=' + data);
				$("#create_room").attr('target', '_blank');
			}
		});
	});
	
	webrtc.connection.on('message', function(username, message) {
		
		//var new_data, textarea, total;
		
		console.log('message');
		console.log(message);
		
		if(typeof message !== 'undefined' && message.message) {
			
			//new_data = message.sent + ' ' + message.from + ' > ' + message.message;
			
			/*
			if (message.to) {
				if (message.to == USERNAME) {
					
					$('#ou_' + u(message.from) + ':not(.messaged,.selected)').addClass('messaged');
					
					//textarea = getUserChatTextArea(message.from); 
				}
			} else if (message.room == ROOM_ID || message.from == 'system') {
				//textarea = document.getElementById('incomingChatMessages');

				$('.me:not(.messaged,.selected)').addClass('messaged');					
			}
			*/
			
			/*
			if(textarea) {
				total = ((textarea.value ? textarea.value + "\n" : "") + new_data).split("\n");
				if (total.length > CHAT_TEXTAREA_MAX_ROW) total = total.slice(total.length - CHAT_TEXTAREA_MAX_ROW);
				textarea.value = total.join("\n");
			}
			*/
			
			if(message.room != 'SERVER') addToChat(username, message.message);
		}
	});							
});