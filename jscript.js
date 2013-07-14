
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
			//if(value.author != 'SERVER') {
				addToChat(value.author, value);
			//}
		});
	}
	
	console.log(data);
	/*if(username != 'SERVER')*/ addToChat(username, data);
});

// listener, whenever the server emits 'updateusers', this updates the username list
socket.on('updateusers', function(unames, ucolors) {
	usernames = unames;	
	usercolors = ucolors;
	
	for(var i in usernames) {
		$('#online-users').append('<option style="color:' + usercolors[i] + ';">' + usernames[i] +  '</option>');
	}
});

socket.on('room_users', function(rusers, room) {

	room_users = rusers;
	
	$('#room-users').empty();
	$('#room-users').append('<option>Users in room</option>');
	
	for(var i in room_users) {
		$('#room-users').append('<option style="color:' + usercolors[room_users[i]] + ';">' + room_users[i] +  '</option>');
	}
	
	var container = document.getElementById('remoteVideos');
	
	var child_arr = container.childNodes;
		
	for(var i = 0; i < child_arr.length; i++) {
	
		var vframe = child_arr[i];
		
		if(vframe && vframe.className == 'vframe') {
			
			console.log(vframe);
			
			var hide = 1;
			
			for(var j in room_users) {
			
				console.log(vframe.childNodes[1].id);
				
				if(vframe.childNodes[1].id == 'local_video' || 
				   vframe.childNodes[1].id == 'ou_' + room_users[j]) { 
					hide = 0;
					break;
				}
			}
			
			if(hide) vframe.style.display = 'none';
		}
	}	

	for(var i in room_users) {
		
		var userv = document.getElementById('ou_' + room_users[i]);
		
		if(! userv) {
		
			var vframe = document.createElement('div');
			vframe.setAttribute('class', 'vframe');	 
			vframe.id = this.id;
			
			userv = document.createElement('a');
			userv.setAttribute('id', 'ou_' + room_users[i]);
			userv.innerHTML = room_users[i];
			
			var video = document.createElement('video');   			   
			video.setAttribute('id', 'ouv_' + room_users[i]); 
			video.setAttribute('class', 'remote_video'); 
			video.setAttribute('poster', '/design/images/qmf.jpg'); 

			vframe.appendChild(video);
			vframe.appendChild(userv);
			
			container.appendChild(vframe);
		
		} else {
			userv.parentNode.style.display = 'block';
		}
	}
});

// listener, whenever the server emits 'updaterooms', this updates the room the client is in
socket.on('updaterooms', function(rooms, current_room) {
	$('#rooms').empty();
	$.each(rooms, function(key, value) {
		//$('#rooms').append("<a onclick=\"switchRoom('" + value + "')\">" + value + "</a>");
		
		//console.log(value);
		
		$('#rooms').empty;
			
		var container = document.getElementById('rooms');

		var vframe = document.createElement('div');
		vframe.setAttribute('class', 'vframe');	 
		vframe.id = 'room_' + value;
		
		var userv = document.createElement('a');
		userv.setAttribute('id', 'room_name_' + value);
		userv.setAttribute('onclick', "switchRoom('" + value + "')");
		userv.innerHTML = value;
		
		var img = document.createElement('img');    
		img.id = 'room_video_' + value;    
		img.setAttribute('class', 'remote_video'); 
		img.setAttribute('src', '/design/images/peep.jpg'); 

		vframe.appendChild(img);
		vframe.appendChild(userv);
		
		container.appendChild(vframe);	
		
		$('#room_name_' + current_room).addClass('curr');	
	});
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