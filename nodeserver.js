var express = require('express'),
    http = require('http');

var app = express();
var server = http.createServer(app).listen(8080);

var io = require('socket.io').listen(server,{log: 0});  // Your app passed to socket.io

// routing
/*
app.get('/', function (req, res) {
  //res.sendfile(__dirname + '/chat.html');
});
*/

var users = [];

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

// usernames which are currently connected to the chat
var usernames = {};
var usercolors = {};

var history = [];

var colors = [ 'red', 'green', 'blue', 'magenta', 'purple', 'plum', 'orange' ];
// in random order
colors.sort(function(a,b) { return Math.random() > 0.5; } );

//console.log(colors);

// rooms which are currently available in chat
var rooms = ['Lobby','Debugging','Test'];

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

io.sockets.on('connection', function (socket) {

	// when the client emits 'adduser', this listens and executes
	socket.on('adduser', function(username){
		
		//console.log('adduser');
		
		// store the username in the socket session for this client
		socket.username = username;
		// store the room name in the socket session for this client
		socket.room = rooms[0];
		// add the client's username to the global list
		usernames[username] = username;

		socket.usercolor = colors.shift();		
		usercolors[username] = socket.usercolor;
		colors.push(socket.usercolor);	//push back the color to the end of the list
				
		//update userlist
		io.sockets.emit('updateusers', usernames, usercolors);
		
		// send client to room
		socket.join(socket.room);
		socket.emit('updaterooms', rooms, socket.room);
		
		// echo to client they've connected
		var msg = username + ' has connected to ' + socket.room;
		socket.emit('updatechat', 'SERVER', msg, history[socket.room]);
		// echo to room 1 that a person has connected to their room
		socket.to(socket.room).emit('updatechat', 'SERVER', msg);
		
		addToHistory(socket.room, (new Date()).getTime(), msg, 'SERVER');

		addRoomUser (socket.room, username);
		
		io.sockets.in(socket.room).emit('room_users', room_users[socket.room], socket.room);	
	});
	
	// when the client emits 'sendchat', this listens and executes
	socket.on('sendchat', function (data) {

		// we tell the client to execute 'updatechat' with 2 parameters
		io.sockets.in(socket.room).emit('updatechat', socket.username, data);
		
		addToHistory(socket.room, data.time, data.text, socket.username);
	});

	socket.on('switchRoom', function(newroom){
		// leave the current room (stored in session)
		socket.leave(socket.room);

		removeRoomUser (socket.room, socket.username);

		io.sockets.in(socket.room).emit('room_users', room_users[socket.room], socket.room);
		
		// sent message to OLD room
		var msg = socket.username +' has left ' + socket.room;		
		socket.broadcast.to(socket.room).emit('updatechat', 'SERVER', msg);
		
		addToHistory(socket.room, (new Date()).getTime(), msg, 'SERVER');
		
		// join new room, received as function parameter
		socket.join(newroom);
		socket.room = newroom;
		
		msg = socket.username +'  has joined to ' + socket.room;
		socket.emit('updatechat', 'SERVER', msg, history[socket.room]);		
		socket.broadcast.to(newroom).emit('updatechat', 'SERVER', msg);

		addToHistory(socket.room, (new Date()).getTime(), msg, 'SERVER');
		
		// update socket session room title
		socket.emit('updaterooms', rooms, newroom);

		addRoomUser (socket.room, socket.username);
	
		io.sockets.in(socket.room).emit('room_users', room_users[socket.room], socket.room);		
	});

	// when the user disconnects.. perform this
	socket.on('disconnect', function(){

		removeRoomUser (socket.room, socket.username);

		io.sockets.in(socket.room).emit('room_users', room_users[socket.room], socket.room);	
		
		// remove the username from global usernames list
		delete usernames[socket.username];
		
		// update list of users in chat, client-side
		io.sockets.emit('updateusers', usernames, room_users);
		
		// echo globally that this client has left
		var msg = socket.username + ' has disconnected'; 
		socket.broadcast.emit('updatechat', 'SERVER', msg);
		socket.leave(socket.room);

		addToHistory(socket.room, (new Date()).getTime(), msg, 'SERVER');
	});
	
	//from nodeserver.js
    // pass a message
    socket.on('message', function (details) {
        var otherClient = io.sockets.sockets[details.to];

        if (!otherClient) {
			socket.broadcast.emit('message', details);
            return;
        }
        delete details.to;
        details.from = socket.id;
        otherClient.emit('message', details);
    });

    socket.on('join', function (room_name, user_name) {
        socket.join(room_name);
		socket.username = user_name;
        io.sockets.in(room_name).emit('joined', {
            roomname: room_name,
            username: socket.username,
            users: users.push2({'id': socket.id, 'name': user_name}),
            clientid: socket.id
        });
    });

    function leave() {
        var rooms = io.sockets.manager.roomClients[socket.id];
		
		//console.log('leave');
		//console.log(client.id);
		
        for (var name in rooms) {
            if (name) {
                io.sockets.in(name.slice(1)).emit('left', {
                    room: name,
                    id: socket.id,
					users: users.remove2(socket.id)
                });
            }
        }
    }

    socket.on('disconnect', leave);
    socket.on('leave', leave);

    socket.on('create', function (name, cb) {
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
            socket.join(name);
            if (cb) cb(null, name);
        }
	});
});