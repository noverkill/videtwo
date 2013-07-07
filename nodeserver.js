var express = require('express'),
    http = require('http');

var app = express();
var server = http.createServer(app).listen(8080);

var io = require('socket.io').listen(server,{log: 1});  // Your app passed to socket.io

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

// rooms which are currently available in chat
var rooms = ['Lobby','Debugging','Test'];

io.sockets.on('connection', function (socket) {

	// when the client emits 'adduser', this listens and executes
	socket.on('adduser', function(username){
		// store the username in the socket session for this client
		socket.username = username;
		// store the room name in the socket session for this client
		socket.room = rooms[0];
		// add the client's username to the global list
		usernames[username] = username;
		// send client to room 1
		socket.join(socket.room);
		// echo to client they've connected
		socket.emit('updatechat', 'SERVER', 'you have connected to ' + socket.room);
		// echo to room 1 that a person has connected to their room
		socket.broadcast.to(socket.room).emit('updatechat', 'SERVER', username + ' has connected to ' + socket.room);
		socket.emit('updaterooms', rooms, socket.room);
		io.sockets.emit('updateusers', usernames);
	});
	
	// when the client emits 'sendchat', this listens and executes
	socket.on('sendchat', function (data) {
		// we tell the client to execute 'updatechat' with 2 parameters
		io.sockets.in(socket.room).emit('updatechat', socket.username, data);
	});

	socket.on('switchRoom', function(newroom){
		// leave the current room (stored in session)
		socket.leave(socket.room);
		// join new room, received as function parameter
		socket.join(newroom);
		socket.emit('updatechat', 'SERVER', 'you have connected to '+ newroom);
		// sent message to OLD room
		socket.broadcast.to(socket.room).emit('updatechat', 'SERVER', socket.username+' has left this room');
		// update socket session room title
		socket.room = newroom;
		socket.broadcast.to(newroom).emit('updatechat', 'SERVER', socket.username+' has joined this room');
		socket.emit('updaterooms', rooms, newroom);
	});

	// when the user disconnects.. perform this
	socket.on('disconnect', function(){
		// remove the username from global usernames list
		delete usernames[socket.username];
		// update list of users in chat, client-side
		io.sockets.emit('updateusers', usernames);
		// echo globally that this client has left
		socket.broadcast.emit('updatechat', 'SERVER', socket.username + ' has disconnected');
		socket.leave(socket.room);
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