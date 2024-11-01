<?php

    class TwitterSN extends SocialNetwork {
        var $consumer_key;
        var $consumer_secret;
        var $user;
        
        function TwitterSN() {
            $this->consumer_key = 'R6PN009MiQiiXwbLz56HA';
            $this->consumer_secret = '7fffQsh7w8bS5el7t7bzN1lWVgI95WK4TjxomYP0hEY';
            
            if($_GET['livestream_auth'] == 'twitter')
                $this->auth();
            if($_GET['livestream_unauth'] == 'twitter'){
                $livestream_options = get_option('live_stream');
                $livestream_social_networks = $livestream_options['social-networks'];
                unset($livestream_social_networks['twitter']);
                $livestream_options['social-networks'] = $livestream_social_networks;
                update_option('live_stream', $livestream_options);
                header('Location: '.$_SERVER['HTTP_REFERER']);
                exit;
            }
        }
        
        function link() {
            return "javascript:;\" onclick=\"window.open('?livestream_auth=twitter', '', 'width=800,height=440');return true;";
        }
        
        function unlink() {
            return "?livestream_unauth=twitter";
        }
        
        function is_authed() {
            include_once "twitter-auth.php";
            $livestream_options = get_option('live_stream');
            $livestream_social_networks = $livestream_options['social-networks'];
            if(!$livestream_social_networks['twitter'])
                return false;
                
            
            $tmhOAuth = new tmhOAuth(array(
                consumer_key => $this->consumer_key,
                consumer_secret => $this->consumer_secret,
                user_token => $livestream_social_networks['twitter']['token'],
                user_secret => $livestream_social_networks['twitter']['secret']
            ));
		
       	    $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
       	
		    $verify = (200 === $tmhOAuth->response['code']);
		    if(!$verify){
		        unset($livestream_social_networks['twitter']);
                $livestream_options['social-networks'] = $livestream_social_networks;
                update_option('live_stream', $livestream_options);
		    } else {
		        $this->user = json_decode($tmhOAuth->response['response']);
		    }
		    return $verify;
        }
        
        private function auth() {
            include_once "twitter-auth.php";
            session_start();
            $tmhOAuth = new tmhOAuth(array(
                consumer_key => $this->consumer_key,
                consumer_secret => $this->consumer_secret
            ));

    		$callback = $tmhOAuth->php_self().'?livestream_auth=twitter';
    		
    		// authorized
            if (isset($_REQUEST['oauth_verifier'])) {
                // use verifier to retrieve tokens
                $tmhOAuth->config['user_token']  = $_SESSION['oauth']['oauth_token'];
	          	$tmhOAuth->config['user_secret'] = $_SESSION['oauth']['oauth_token_secret'];
	
	          	$tmhOAuth->request('POST', $tmhOAuth->url('oauth/access_token', ''), array(
	            		'oauth_verifier' => $_REQUEST['oauth_verifier']
	          	));
	      	
	      	    // store tokens
	      	    $_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
	      	    
                $livestream_options = get_option('live_stream');
                $livestream_social_networks = $livestream_options['social-networks'];
                $livestream_social_networks['twitter'] = array (
                    'token' => $_SESSION['oauth']['oauth_token'],
                    'secret' => $_SESSION['oauth']['oauth_token_secret']
                );
                $livestream_options['social-networks'] = $livestream_social_networks;
                update_option('live_stream', $livestream_options);
                unset($_SESSION['oauth']);
                ?>
			<script type="text/javascript" >
			    if(window.opener.jQuery('body').hasClass('wp-admin'))
    				window.opener.location.reload();
    		    else
    		        window.opener.jQuery('.livestream_widget_border .twitter input.noauth, .livestream_widget_border .twitter div.noauth').removeClass('noauth')
				window.close();
			</script>
			<?php
            } else {
                // send auth request
                $tmhOAuth->request('POST', $tmhOAuth->url('oauth/request_token', ''), array(
	            		'oauth_callback' => $callback
	          	));
	
	          	if ($tmhOAuth->response['code'] == 200) {
	            		$_SESSION['oauth'] = $tmhOAuth->extract_params($tmhOAuth->response['response']);
	            		$method = 'authorize';
	            		//$force  = isset($_REQUEST['force']) ? '&force_login=1' : '';
	            		//$forcewrite  = isset($_REQUEST['force_write']) ? '&oauth_access_type=write' : '';
	            		//$forceread  = isset($_REQUEST['force_read']) ? '&oauth_access_type=read' : '';
	            		header("Location: " . $tmhOAuth->url("oauth/{$method}", '') .  "?oauth_token={$_SESSION['oauth']['oauth_token']}{$force}{$forcewrite}{$forceread}");
	
	          	} else {
	            	// error
	            		$tmhOAuth->pr(htmlentities($tmhOAuth->response['response']));
	          	}
	        }
            exit;
        }        
        
        private function get_user_details() {
            if($this->user)
                return $this->user;
            include_once "twitter-auth.php";
                
            $livestream_options = get_option('live_stream');
            $livestream_social_networks = $livestream_options['social-networks'];
                
            $tmhOAuth = new tmhOAuth(array(
                consumer_key => $this->consumer_key,
                consumer_secret => $this->consumer_secret,
                user_token => $livestream_social_networks['twitter']['token'],
                user_secret => $livestream_social_networks['twitter']['secret']
            ));
		
       	    $tmhOAuth->request('GET', $tmhOAuth->url('1/account/verify_credentials'));
			$this->user = json_decode($tmhOAuth->response['response']);
			
		  	return $this->user;
        }
        
        function username() {
            return $this->get_user_details()->screen_name;
        }
        
        function update($post) {
            include_once "twitter-auth.php";
            $livestream_options = get_option('live_stream');
            $livestream_social_networks = $livestream_options['social-networks'];
            
            $tmhOAuth = new tmhOAuth(array(
                consumer_key => $this->consumer_key,
                consumer_secret => $this->consumer_secret,
                user_token => $livestream_social_networks['twitter']['token'],
                user_secret => $livestream_social_networks['twitter']['secret']
            ));
            
            $tmhOAuth->request('POST', $tmhOAuth->url('1.1/statuses/update'), array(
			    'status' => $post
    		));
        }
    }
    
    liveStream_register_social_network('twitter', new TwitterSN);

?>
