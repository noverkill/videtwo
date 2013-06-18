/*global console*/
var yetify = require('yetify'),
    config = require('getconfig'),
    uuid = require('node-uuid'),
    io = require('socket.io').listen(config.server.port, {log: 1});

var usernames = [];

io.sockets.on('connection', function (client) {
    // pass a message
    client.on('message', function (details) {
            
        var otherClient = io.sockets.sockets[details.to];

        if (!otherClient) {
            console.log('Message Received: ', details);
            client.broadcast.emit('message', details);
            return;
        }
        
        delete details.to;
        
        details.from = client.id;
        
        otherClient.emit('message', details);
    });

    client.on('adduser', function(username){
        console.log(username);
        client.username = username;
        usernames.push(username);
        //client.broadcast.emit('userconnected', username);
        io.sockets.emit('userconnected', usernames);
    });
    
    client.on('join', function (name) {
        client.join(name);
        io.sockets.in(name).emit('joined', {
            room: name,
            id: client.id
        });
    });

    client.on('disconnect',function() {
	//insert data corresponding to current socket into database
	console.log('The client has disconnected!');
	console.log(client.id);
	console.log(client.username);
	usernames.splice(usernames.indexOf(client.username), 1);
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
