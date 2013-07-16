/*global console*/
var yetify = require('yetify'),
    config = require('getconfig'),
    uuid = require('node-uuid'),
    io = require('socket.io').listen(config.server.port, {log: 1});


var users = [];
var usernames = []; // it is {} in the new one!!!
var usercolors = {};
var history = [];

var colors = [ 'red', 'green', 'blue', 'magenta', 'purple', 'plum', 'orange' ];

// randomize order
colors.sort(function(a,b) { return Math.random() > 0.5; } );

// rooms currently available in chat
var rooms = ['Lobby','Debugging','Test'];

Array.prototype.push2 = function(a){
	this.push(a);
	return this;
}

Array.prototype.remove2 = function(id){
	//console.log(id);
    var idx = -1;	
	for (var i = 0; i < this.length; i++) {
        if (this[i]['id'] == id) {
            idx = i;
			break;
        }
    }
	//console.log(idx);
	if( idx > -1) this.splice(idx, 1);
	//console.log(this);
	return this;
}

function addToHistory (room, time, msg, author) {
	
	var obj = {
		time: time,
		text: msg,
		//text: htmlEntities(msg.utf8Data),
		author: author
	};
	
	if(typeof history[room] === 'undefined') history[room] = [];
	
	history[room].push(obj);
	history[room] = history[room].slice(-100);
}

var room_users = [];

function addRoomUser (room, user) {
	
	room_users[room] = room_users[room] || [];

	var index = room_users[room].indexOf(user);
	
	if(index < 0) {
		room_users[room].push(user);
	}
}

function removeRoomUser (room, user) {
	
	room_users[room] = room_users[room] || [];
	
	var index = room_users[room].indexOf(user);
	
	if (index > -1) {
	
		var usr = room_users[room][index];
	
		room_users[room].splice(index, 1);

		delete usr;
	}
}

io.sockets.on('connection', function (client) {

    // pass a message
    client.on('message', function (details) {
            
        var otherClient = io.sockets.sockets[details.to];

        if (!otherClient) return;
		
        details.from = client.id;
        
        otherClient.emit('message', details);
    });
	
	// when the client emits 'adduser', this listens and executes
	client.on('adduser', function(username){
		
		client.username = username;

		//##client.room = rooms[0];

		usernames[username] = username;

		client.usercolor = colors.shift();		
		usercolors[username] = client.usercolor;
		colors.push(client.usercolor);	//push back the color to the end of the list
				
		io.sockets.emit('updateusers', usernames, usercolors);
		
		//##client.join(client.room);
		//##client.emit('updaterooms', rooms, client.room);
		client.emit('updaterooms', rooms);
		
		//##var msg = username + ' has entered into ' + client.room;
		//##var time = (new Date()).getTime();
		
		//##io.sockets.in(client.room).emit('updatechat', 'SERVER', {'text': msg, 'time': time}, history[client.room]);
		
		//##addToHistory(client.room, time, msg, 'SERVER');

		//##addRoomUser (client.room, username);
		
		//##io.sockets.in(client.room).emit('room_users', room_users[client.room], client.room);	
	});

	// when the client emits 'sendchat', this listens and executes
	client.on('sendchat', function (data) {

		// we tell the client to execute 'updatechat' with 2 parameters
		io.sockets.in(client.room).emit('updatechat', client.username, data);
		
		addToHistory(client.room, data.time, data.text, client.username);
	});

	client.on('switchRoom', function(newroom){
		// leave the current room (stored in session)
		client.leave(client.room);

		removeRoomUser (client.room, client.username);

		io.sockets.in(client.room).emit('room_users', room_users[client.room], client.room);
		
		// sent message to OLD room
		var msg = client.username +' has left ' + client.room;	
		var time = (new Date()).getTime();
		
		client.broadcast.to(client.room).emit('updatechat', 'SERVER', {'text': msg, 'time': time});
		
		addToHistory(client.room, time, msg, 'SERVER');
		
		// join new room, received as function parameter
		client.join(newroom);
		client.room = newroom;
		
		msg = client.username +'  has entered into ' + client.room;
		time = (new Date()).getTime();
		
		client.emit('updatechat', 'SERVER', {'text': msg, 'time': time}, history[client.room]);		
		client.broadcast.to(newroom).emit('updatechat', 'SERVER', {'text': msg, 'time': time});

		addToHistory(client.room, time, msg, 'SERVER');
		
		// update socket session room title
		client.emit('updaterooms', rooms, newroom);

		addRoomUser (client.room, client.username);
	
		io.sockets.in(client.room).emit('room_users', room_users[client.room], client.room);		
	});
	
	client.on('leaveRoom', function(newroom){
	
		client.leave(client.room);

		removeRoomUser (client.room, client.username);

		io.sockets.in(client.room).emit('room_users', room_users[client.room], client.room);
		
		// sent message to OLD room
		var msg = client.username +' has left ' + client.room;	
		var time = (new Date()).getTime();
		
		client.broadcast.to(client.room).emit('updatechat', 'SERVER', {'text': msg, 'time': time});
		
		addToHistory(client.room, time, msg, 'SERVER');
	});

	client.on('enterRoom', function(newroom){
		// join new room, received as function parameter
		client.join(newroom);
		client.room = newroom;
		
		msg = client.username +'  has entered into ' + client.room;
		time = (new Date()).getTime();
		
		client.emit('updatechat', 'SERVER', {'text': msg, 'time': time}, history[client.room]);		
		client.broadcast.to(newroom).emit('updatechat', 'SERVER', {'text': msg, 'time': time});

		addToHistory(client.room, time, msg, 'SERVER');
		
		// update socket session room title
		client.emit('updaterooms', rooms, newroom);

		addRoomUser (client.room, client.username);
	
		io.sockets.in(client.room).emit('room_users', room_users[client.room], client.room);		
	});
	
	/*
    client.on('join', function (name) {
        client.join(name);
        io.sockets.in(name).emit('joined', {
            room: name,
            id: client.id
        });
    });
	*/
	
    client.on('join', function (room_name, user_name) {
        client.join(room_name);
		client.username = user_name;
        io.sockets.in(room_name).emit('joined', {
            roomname: room_name,
            username: client.username,
            users: users.push2({'id': client.id, 'name': user_name}),
            clientid: client.id
        });
    });
	
	// when the user disconnects.. perform this
	client.on('disconnect', function(){

		removeRoomUser (client.room, client.username);

		io.sockets.in(client.room).emit('room_users', room_users[client.room], client.room);	
		
		// remove the username from global usernames list
		delete usernames[client.username];
		
		// update list of users in chat, client-side
		io.sockets.emit('updateusers', usernames, room_users);
		
		// echo globally that this client has left
		var msg = client.username + ' has disconnected'; 
		var time = (new Date()).getTime();
		
		client.broadcast.emit('updatechat', 'SERVER', {'text': msg, 'time': time});
		client.leave(client.room);

		addToHistory(client.room, time, msg, 'SERVER');
		
		io.sockets.emit('userdeleted', client.username);
	});
	
    function leave() {
        var rooms = io.sockets.manager.roomClients[client.id];
        for (var name in rooms) {
            if (name) {
                io.sockets.in(name.slice(1)).emit('left', {
                    room: name,
                    id: client.id
                });
            }
        }
    }

    client.on('disconnect', leave);
    client.on('leave', leave);

    client.on('create', function (name, cb) {
        if (arguments.length == 2) {
            cb = (typeof cb == 'function') ? cb : function () {};
            name = name || uuid();
        } else {
            cb = name;
            name = uuid();
        }
        // check if exists
        if (io.sockets.clients(name).length) {
            cb('taken');
        } else {
            client.join(name);
            if (cb) cb(null, name);
        }
    });
});

if (config.uid) process.setuid(config.uid);
console.log(yetify.logo() + ' -- signal master is running at: http://localhost:' + config.server.port);
