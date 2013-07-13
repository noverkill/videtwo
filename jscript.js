
var usernames = [];
var usercolors = [];

var room_users = [];

var remotevid = null;

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

//escape usernames before use with jQuery id selector
//(because the dot in the username creates problems)
u = function (n) {
	return n.replace(/\./gi, "\\.");
}
	
var socket = io.connect('http://videtwo.com:8080');

socket.on('connect', function(){
	socket.emit('adduser', USERNAME);
});

socket.on('updatechat', function (username, data, history) {
	
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
	usercolors = ucolors;
	
	for(var i in usernames) {
		$('#online-users').append('<option style="color:' + usercolors[i] + ';">' + usernames[i] +  '</option>');
	}
});

socket.on('room_users', function(rusers, room) {

	room_users = rusers;
	
	console.log(room_users);
	
	$('#room-users').empty();
	$('#room-users').append('<option>Users in room</option>');
	
	for(var i in room_users) {
		$('#room-users').append('<option style="color:' + usercolors[room_users[i]] + ';">' + room_users[i] +  '</option>');
		
		var user_video = document.getElementById('ou_' + room_users[i]);
		
		if (! user_video) {
			
			var container = document.getElementById('remoteVideos');
	
			var vframe = document.createElement('div');
			vframe.setAttribute('class', 'vframe');	 
			vframe.id = this.id;
			
			var userv = document.createElement('a');
			userv.setAttribute('id', 'ou_' + room_users[i]);
			userv.setAttribute('onclick', "showUserChat('" + room_users[i] + "', 1)");
			userv.innerHTML = room_users[i];
			
			var video = document.createElement('video');    
			video.id = 'ou_' + room_users[i];    
			video.setAttribute('class', 'remote_video'); 
			video.setAttribute('poster', '/design/images/qmf.jpg'); 

			remotevid = video;

			vframe.appendChild(video);
			vframe.appendChild(userv);
			
			container.appendChild(vframe);
		}
	}
});

// listener, whenever the server emits 'updaterooms', this updates the room the client is in
socket.on('updaterooms', function(rooms, current_room) {
	$('#rooms').empty();
	$('#rooms').append('<option>Switch room</option>');
	$.each(rooms, function(key, value) {
		$('#rooms').append('<option>' + value + '</option>');
	});
	$('#rooms').val(current_room);
});

function switchRoom(room){
	socket.emit('switchRoom', room);
	$('#incomingChatMessages').empty();
}

function addToChat(username, data) {
	
	var dt = new Date(data.time);
	
	var incomingChatMessages = $('#incomingChatMessages');
	
	if(incomingChatMessages.children().length > 9) incomingChatMessages.find('p:first').remove();
	
	incomingChatMessages.append('<p><span style="color:' + usercolors[username] + '">' + username + '</span> @ ' +
             + (dt.getHours() < 10 ? '0' + dt.getHours() : dt.getHours()) + ':'
             + (dt.getMinutes() < 10 ? '0' + dt.getMinutes() : dt.getMinutes()) + ':'
             + (dt.getSeconds() < 10 ? '0' + dt.getSeconds() : dt.getSeconds())
             + ': ' + data.text + '</p>');
			 
}

var webrtc = new WebRTC({
	remoteVideosEl: 'remoteVideos',
	// immediately ask for camera access
	autoRequestMedia: true
});

// we have to wait until it's ready
webrtc.on('readyToCall', function () {
		 
	$('.me').html(USERNAME);
	
	webrtc.joinRoom(ROOM_ID, USERNAME);
});
					
$(function(){
  
	$('#rooms').change(function(e) {
		switchRoom(this.options[this.selectedIndex].value);
	});
	
	$('#outgoingChatMessage').keypress(function(e) {
		if(e.which == 13) {
			
			var obj = {
				time: (new Date()).getTime(),
				text: $(this).val()
			};

			$(this).val('');
			
			socket.emit('sendchat', obj);
		}
	});
	 
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
});