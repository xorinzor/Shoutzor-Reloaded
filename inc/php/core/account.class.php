<?php

class account {
    public function __CONSTRUCT() {
        
    }
    
    /**
     * Create an account
     */
    private function create($args) {
        try {
            if(security::valid("TEXT", $args['firstname']) === false)       throw new Exception($_LANG['error']['registration']['invalid_firstname']);
            if(security::valid("TEXT", $args['lastname']) === false)        throw new Exception($_LANG['error']['registration']['invalid_lastname']);
            if(security::valid("EMAIL", $args['email']) === false)          throw new Exception($_LANG['error']['registration']['invalid_email']);
            if(security::valid("PASSWORD", $args['password']) === false)    throw new Exception($_LANG['error']['registration']['invalid_password']);
            
            $firstname  = $args['firstname'];
            $lastname = $args['lastname'];
            $email      = $args['email'];
            $password   = security::encryptOneWay('password', $args['password']);
            
            if($this->exists($email)) throw new Exception(sprintf($_LANG['error']['registration']['account_exists'], SITEURL."login/"));
            
            $query = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."users` (name, firstname, email, password, status) VALUES ('$lastname', '$firstname', '$email', '$password', '0')") or debug::addLine("error", "An error occured while attempting to create a new account, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
            if(!$query) throw new Exception("An internal server error occured while attempting to create your account, please try again alter");
            
            $uid    = cl::g("mysql")->insert_id;
            $token  = md5(microtime() . "!4 v3ry s3cret t0ken!");
            
            $query  = cl::g("mysql")->query("INSERT INTO `".SQL_PREFIX."users_verification_token` VALUES ($uid, '$token')") or debug::addLine("error", "An error occured while attempting to create a new account, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
            if(!$query) throw new Exception("An internal server error occured while attempting to create your account, please try again alter");
            
            return array(
                    "result" => true,
                    "id" => $uid,
                    "token" => $token
                );
        } catch(Exception $e) {
            $error = $e->getMessage();
            return array(
                    "result" => false,
                    "msg" => $error
                );
        }
    }
    
    /**
     * Checks if an account already exists in the database
     */
    private function exists($email) {
        if(!security::valid("EMAIL", $email)) $email = 'invalid';
        
        $query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."users` WHERE email='$email'") or debug::addLine("error", "An error occured while attempting to check if the account already exists, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
        if($query):
            return ($query->num_rows == 0) ? false : true;
        endif;
        
        return false;
    }
    
    /**
     * Login with the given credentials
     * 
     * Social network account args:
     * - Accesstoken
     * - other OAuth params
     * 
     * MSS-Account Args:
     *  - Email
     *  - Password
     */
    public function login($args) {
        global $_LANG;
        try {
            if($args['loginmethod'] == 'socialnetwork'):
                $uid = $this->isConnected($args['socialnetwork']);
                if($uid == false):
                    //Invalid login
                else:
                    cl::g("session")->forceLogin($uid);
                endif;
            else:
                //Sanitize input
                if(security::valid("EMAIL", $args['email']) === false)          throw new Exception($_LANG['error']['registration']['invalid_email']);
                if(security::valid("PASSWORD", $args['password']) === false)    throw new Exception($_LANG['error']['registration']['invalid_password']);
                
                $email              = $args['email'];
                $password           = security::encryptOneWay('password', $args['password']);
                $remembersession    = ($args['remembersession'] == 1) ? true : false;
                $captcha            = $args['captcha'];
                    
                //Check if account exists
                if($this->exists($args['email'])):
                    $result = cl::g("session")->login($email, $password, $remembersession, $captcha);
                    if($result):
                        //Login succes
                        return array(
                                "result" => true,
                                "msg" => "Login succes"
                            );
                    else:
                        //Login failed
                        throw new Exception("Invalid login, please try again");
                    endif;
                else:
                    throw new Exception("The provided username does not exist");
                endif;
            endif;
        } catch(Exception $e) {
            return array(
                    "result" => false,
                    "msg" => $e->getMessage()
                );
        }
    }
    
    /**
     * Registers an account
     * 
     * MSS-Account Args:
     *  - Firstname
     *  - Familyname
     *  - Email
     *  - Password
     */
    public function register($args) {
        global $_LANG; 
        if($this->exists($args['email'])):
            return array(
                    "result" => false,
                    "msg" => sprintf($_LANG['error']['registration']['account_exists'], SITEURL."login/")
                );
        else:
            //Create the mysocialsync account
            $created = $this->create($args);
            if($created['result'] === true):
                
                $params = array(
                            'sprintf' => array(
                                            SITEURL . "register/verify/?token=".$created['token']."&uid=".$created['id']
                                        )
                        );
                
                $mailSent = cl::g("mail")->sendMail('validateregistration', $args['email'], $params);
                if($mailSent === false):
                    //Mail could not be sent for some reason, add to cache
                    $query = cl::g("mysql_mail")->query("INSERT INTO `mail_cache` VALUES ('validateregistration', '".$args['email']."', '".json_encode($params)."' NOW())") or debug::addLine("FATAL", "An mysql error occured: ".cl::g("mysql_mail")->getError(), __FILE__, __LINE__);
                    if(!$query):
                        //Think of some failsafe here..
                    endif;
                endif;
                
                return array(
                        "result" => true,
                        "msg" => "Account created"
                    );
            else:
                return array(
                        "result" => false,
                        "msg" => $created
                    );
            endif;
        endif;
    }
    
    public function connectAccount($args) {
        if(cl::g("session")->isLoggedin()):
            //Security check
            if(!security::valid('NUMERIC', $aid)) return false;
            
            $uid = cl::g("session")->getUserID();
            
            switch($args['socialnetwork']):
                case 'facebook':
                    //User is loggedin, connect.
                    $account = new facebookAccount($args['aid']);
                    if(!$account->getIsValidSession()):
                        //Account does not yet exist in the database, create & connect it
                        $accesstoken    = $args['accesstoken'];
                        $expires        = $args['expires'];
                        $signedrequest  = $args['signed_request'] ;
                        
                        $query = cl::g("mysql_socialdata")->query("INSERT INTO `fb_accounts` ('fbuid', 'accesstoken', 'expires', 'signed_request') VALUES ($aid, '', '', '')") or debug::addLine("FATAL", "An mysql error occured: ".cl::g("mysql_socialdata")->getError(), __FILE__, __LINE__);
                        if(!$query):
                            //Query failed, display error
                            return array(
                                    "result" => false,
                                    "connected" => false
                                );
                        endif;
                    endif;
                    
                    //Account already exists in the database, check if it is already connected to an account (currently only one connection per account is allowed)
                    $check = cl::g("mysql_socialdata")->query("SELECT * FROM `account_connect` WHERE aid='$aid'") or debug::addLine("FATAL", "An mysql error occured: ".cl::g("mysql_socialdata")->getError(), __FILE__, __LINE__);
                    if($check->num_rows == 0):
                        //Account is not yet connected to any mysocialsync account, connect it.
                        $query = cl::g("mysql_socialdata")->query("INSERT INTO `account_connect` ('uid', 'aid', 'type') VALUES ($uid, $aid, 'facebook')") or debug::addLine("FATAL", "An mysql error occured: ".cl::g("mysql_socialdata")->getError(), __FILE__, __LINE__);
                        if($query):
                            return array(
                                    "result" => true,
                                    "connected" => true
                                );
                        else:
                            //Query failed, display error
                            return array(
                                    "result" => false,
                                    "connected" => false
                                );
                        endif;
                    else:
                        //Account is already connected, display error.
                        return array(
                                "result" => false,
                                "connected" => true
                            );
                    endif;
                break;
                
                default:
                    return array(
                            "result" => false,
                            "connected" => false
                        );
                break;
            endswitch;
        else:
            return array(
                    "result" => false,
                    "connected" => false
                );
        endif;
    }
    
    /**
     * Function to check if a given social network is already connected to an account
     */
    private function isConnected($socialNetwork, $args = array()) {
        /**
         * 1. Look for given accesstoken in the correct socialnetworkuser-table
         * 2. create session for the user that is connected to it
         */
        switch($socialNetwork):
            case "facebook":
                if($args['account'] instanceof facebookAccount):
                    $fb = $args['account']->getFacebookInstance();
                else:
                    $fb = new Facebook(array(
                        'appId'  => FB_APP_ID,
                        'secret' => FB_APP_SECRET,
                    ));
                endif;
                
                $uid = $fb->getUser();
                if($uid == 0 || !security::valid('NUMERIC', $uid)) return false;
                
                $query = cl::g("mysql_socialdata")->query("SELECT uid FROM `account_connect` WHERE aid IN (SELECT id FROM `fbaccounts` WHERE fbuid='$uid') and type='facebook'") or debug::addLine("FATAL", "An mysql error occured: ".cl::g("mysql_socialdata")->getError(), __FILE__, __LINE__);
                if($query->num_rows == 0) return false;
                
                $user = $query->fetch_object();
                return $user->uid;
            break;
                
            case "twitter":
                break;
                
            default:
                return false;
                break;
        endswitch;
    }
}
