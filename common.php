<?php 

error_reporting(E_ALL);
ini_set('display_errors', 1);

//print 'whoami: ' . exec('whoami') . '<br>';

if (! is_dir("users")) mkdir("users");
    
function login($user) {

    $ufile = "users/".$user->id;
    //debug("ufile: $ufile");

    if(! is_file($ufile)){
        if (! file_put_contents($ufile, (serialize($user)))) 
            throw new Exception('Cannot write file');
        debug("user not exists");
    } else {
        debug("user exists");
    }

    $_SESSION['username'] = $user->username;    
}

function debug($message) {
    print "$message<br>";
}

?>