<?php

class Yahoo extends Oauth {
 
    protected $_prefix = 'yahoo';
    protected $_authorize_url = 'https://api.login.yahoo.com/oauth/v2/request_auth';
    protected $_access_token_url = 'https://api.login.yahoo.com/oauth/v2/get_token';
    protected $_request_token_url = 'https://api.login.yahoo.com/oauth/v2/get_request_token';

    /*
    public function requestAccessToken($method = 'POST', Array $params = array(), $returnType = 'flat', Array $values = array('oauth_token', 'oauth_token_secret')){
    
        parent::requestAccessToken($method, $params, $returnType, $values);
    }
    */

    protected function requestAccessToken($method = 'GET', Array $params = array(), $returnType = 'flat', Array $values = array('access_token', 'expires')){
        
        print "requestAccessToken";
        
        $parameters = array(
            "oauth_consumer_key" => $this->_client_id, 
            "oauth_signature_method" => "plaintext",             
            "oauth_version" => "1.0",
            "oauth_verifier" => $_GET['oauth_verifier'],
            "oauth_token" => $_SESSION[$this->_prefix]['token'],
            "oauth_nonce" => "8B9SpF",  
            "oauth_timestamp" => time(),  
            "oauth_signature" => $this->_client_secret. '&' . $_SESSION[$this->_prefix]['token_secret']       
        );
        
        //exit;
        
        // make the request
        $response = $this->makeRequest($this->_access_token_url, $method, $parameters, $returnType, false);

        // get the correct parameters from the response
        $params = $this->getParameters($response, $returnType);

        // add the token to the session
        if(isset($params[$values[0]]) && isset($params[$values[1]])){
            if(isset($this->_request_token_url) && strlen($this->_request_token_url) > 0){
                $_SESSION[$this->_prefix]['access_token'] = $params[$values[0]];
                $_SESSION[$this->_prefix]['access_token_secret'] = $params[$values[1]];
            } else {
                $_SESSION[$this->_prefix]['access_token'] = $params[$values[0]];
                $_SESSION[$this->_prefix]['expires'] = time() + $params[$values[1]];
            }
        } else {    // throw exception if incorrect parameters were returned
            $s = '';
            foreach($params as $k => $v){$s = $k . '=' . $v;}
            throw new Exception('incorrect access token parameters returned: ' . implode('&', $s));
        }

        print __FILE__ . " " . __LINE__ . "<br>";
        
        print "params:<br><pre>";
        print_r($params);
        print "</pre>";

        print "session:<br><pre>";
        print_r($_SESSION);
        print "</pre>";
    }
        
    protected function requestToken($returnType = 'flat', Array $values = array('oauth_token', 'oauth_token_secret')){

        print "requestToken<br>";
        print "_request_token_url: " . $this->_request_token_url . "<br>";

        $params = array(
            "oauth_nonce" => "ce2130523f788f313f76314ed3965ea6",  
            "oauth_timestamp" => time(),  
            "oauth_consumer_key" => $this->_client_id,  
            "oauth_signature_method" => "plaintext",  
            "oauth_signature" => $this->_client_secret,  
            "oauth_version" => "1.0",  
            "xoauth_lang_pref" => "en-us",  
            "oauth_callback" => $this->_callback        
        );
        
        // make the request
        $response = $this->makeRequest($this->_request_token_url, 'POST', $params, $returnType, true);

        // get the correct parameters from the response
        $params = $this->getParameters($response, $returnType);

        // add the token and token secret to the session
        if(isset($params[$values[0]]) && isset($params[$values[1]])){
            $_SESSION[$this->_prefix]['token'] = $params[$values[0]];
            $_SESSION[$this->_prefix]['token_secret'] = $params[$values[1]];
        } else { // throw exception if incorrect parameters were returned
            $s = '';
            foreach($params as $k => $v){$s = $k . '=' . $v;}
            throw new Exception('incorrect access token parameters returned: ' . implode('&', $s));
        }

        print "params:<br><pre>";
        print_r($params);
        print "</pre>";

        print "session:<br><pre>";
        print_r($_SESSION);
        print "</pre>";

        //exit;
  
    }

}
