<?php

cl::g("display")->setThemeEnabled(false);

switch($_GET['var1']):
    case "verify":
        $uid    = (int) cl::g("core")->GetVars['uid'];
        $token  = (string) cl::g("core")->GetVars['token'];
                
        //Defaults
        $secure = true;
        
        //Security checks
        if(!cl::g("security")->valid("NUMERIC", $uid))          $secure = false;
        if(!cl::g("security")->valid("ALPHANUMERIC", $token))   $secure = false;
                
        //Validate token & activate account
        if($secure === true):
            $query = cl::g("mysql")->query("SELECT uid FROM `".SQL_PREFIX."users_verification_token` WHERE uid='".cl::g("mysql")->mres($uid)."' and token='".cl::g("mysql")->mres($token)."'") or debug::addLine("error", "An error occured while attempting to create a new account, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
            if($query->num_rows == 1):
                $query = cl::g("mysql")->query("UPDATE `".SQL_PREFIX."users` SET status='1' WHERE id='".cl::g("mysql")->mres($uid)."'") or debug::addLine("error", "An error occured while attempting to create a new account, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
                if($query):
                    cl::g("mysql")->query("DELETE FROM `".SQL_PREFIX."users_verification_token` WHERE uid='".cl::g("mysql")->mres($uid)."'") or debug::addLine("error", "An error occured while attempting to create a new account, error: ".cl::g("mysql")->getError(), __FILE__, __LINE__);
                    echo json_encode(array("result" => true, "error" => "You have been verified and you can now login"));
                else:
                    echo json_encode(array("result" => false, "error" => "An internal server error occured while attempting to verify you"));
                endif;
            endif;
        else:
            echo json_encode(array("result" => false, "error" => "Invalid details provided, please make sure you use the URL from the email"));
        endif;

        die();
        break;
        
    default:
        if($_SERVER['REQUEST_METHOD'] == "POST"):
        
            $args = array(
                "firstname" => $_POST['firstname'],
                "lastname" => $_POST['lastname'],
                "email" => $_POST['email'],
                "password" => $_POST['password']
            );
        
            $safety = true;
        
            if(!security::valid('EMAIL', $args['username']) && $safety):
                $error = $_LANG['error']['registration']['invalid_username'];
                $safety = false;
            endif;

            if(!security::valid('EMAIL', $args['email']) && $safety):
                $error = $_LANG['error']['registration']['invalid_email'];
                $safety = false;
            endif;
                        
            if($safety === true):
                $result = cl::g("account")->register($args);

                if($result['result'] === true):
                    //Account creation succes
                    echo json_encode(array("result" => true));
                else:
                    //Account creation failed, display error
                    echo json_encode(array("result" => false, "error" => $result['msg']));
                endif;

                die();
            else:
                echo json_encode(array("result" => false, "error" => $error));
                die();
            endif;
        endif;
    break;
endswitch;

die(json_encode(array("result" => false, "error" => 'Invalid request')));