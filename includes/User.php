<?php

abstract class User {
    public $id;
    public $name;
    public $username;
    public $email;
    public $picture;
    public $gender;
    public $birthdate;
    public $locale;
    public $verified;
    public $facebook_id;
    public $twitter_id;
    public $google_id;
}

class FacebookUser extends User { 
    public function __construct ($details) {
        $this->id = $details['username'];
        $this->name = $details['name'];
        $this->username = $details['username'];
        $this->email = $details['email'];
        $this->gender = $details['gender'];
        $this->facebook_id = $details['id'];    
    }
}

class TwitterUser extends User { 
    public function __construct ($details) {
        $this->id = $details['screen_name'];
        $this->name = $details['name'];
        $this->username = $details['screen_name'];
        $this->twitter_id = $details['id'];    
        
        /* how can I get these two from bloody twitter
        $this->email = $details['email'];
        $this->gender = $details['gender']        
        */
    }
}

class GoogleUser extends User { 
    public function __construct ($details) {
        $this->id = explode('@', $details['email'])[0];
        $this->name = $details['name'];
        $this->username = $this->id;
        $this->email = $details['email'];
        $this->gender = $details['gender'];
        $this->birthdate = $details['birthdate'];
        $this->verified = $details['email_verified'];
        $this->locale = $details['locale'];
        $this->picture = $details['picture'];
        $this->google_id = $details['sub'];    
    }
}
    
?>
