<?php
class userController extends Controller
{
    public $password_required = array('test');
    public $admin_required = array();
    
    
    
    function _default()
    {
            echo "haa";
    }
    

    public function _login()
    {
        $dat = $this->Command->getData();
        
        if ($dat->u) { 
            $u = $dat->u;
            $check_credentials = $this->db->select("users")->where("login = '$u'", "or")->where("email = '$u'", "or")->single();
        
            if ($check_credentials->password) {
                // account with that username/email/email2 have been found
                if ($check_credentials->password == md5($dat->p)) {
                    // password found!
                    
                    // on login, invalidate all previous auth tokens
                    $this->db->delete("auth_codes")->where("user_id = '".$check_credentials->id."'")->go();
                 
                    // generate auth code here
                    $check_credentials->authCode = $this->gen_auth_code($check_credentials->id);
                    $ret = array('result' => 1, 
                                 'user' => $this->strip_sensitive($check_credentials));
                } else {
                    $ret = array('result' => 0,
                                 'reason' => LOGIN_ERROR_BAD_PASSWORD);
                }
            } else {
                // no account with that username/email/email2 have been found
                $ret = array('result' => 0,
                             'reason' => LOGIN_ERROR_BAD_USER);
            
            } 
                
        } else {
            $ret = array('result' => 0,
                         'reason' => LOGIN_ERROR_NO_DATA);
        }
        

        echo json_encode($ret);
    }
   
    public function _logout()
    {
        $dat = $this->Command->getData();
        
        if ($dat->authCode) {
            // get authcode that's been passed, delete it
            $del = $this->expireAuthToken();
        
            if ($del) {
                $ret = array('result' => 1);
            } else {
                $ret = array('result' => 0,
                             'reason' => LOGOUT_ERROR_GENERIC);
            } 
        } else {
            $ret = array('result' => 0,
                         'reason' => LOGOUT_ERROR_NO_AUTHCODE);
        }
        
        echo json_encode($ret);
    }
    
    public function _recover_password()
    {
        $dat = $this->Command->getData();
        
        if ($dat->email) {
            $expire = new DateTime();
            $expire->add(new DateInterval('PT'.RECOVER_PASSWORD_CUTOFF.'S'));
            $qq = $this->db->select("users")->where("email = '".$dat->email."'")->getQuery();
            $user = $this->db->select("users")->where("email = '".$dat->email."'")->single();
        
            if ($user->email > '') {
                $recovery_code = get_random_string(RECOVER_PASSWORD_CODE_LENGTH);
                $recovery_url = SERVER_URL.'?rp='.$recovery_code;
                
                // add recovery to db
                $add_recovery = $this->db->insert("password_recoveries")
                    ->pair('code', $recovery_code)
                    ->pair('user_id', $user->id)
                    ->pair('expires', $expire->format('Y-m-d H:i:s'))->go();
                
                
                if ($add_recovery) {
                    
                    $obj = array('type' => 'recover_password',
                                 'params' => array('name' => $user->name,
                                                   'to' => $user->email, 
                                                   'code' => $recovery_url,
                                                    'subject' => PASSWORD_RECOVERY_SUBJECT)
                                );
                    $obj = (object)$obj;
                    
                   $em = $this->email->_send($obj);
                    
                    if ($em->result == 1) {
                        $ret = array('result' => 1,
                                    'msg' => $em->msg);
                    } else {
                        $ret = array('result' => 0,
                                     'reason' => $em->reason,
                                    'msg' => $em->msg);
                    }
                } else {
                    $ret = array('result' => 0,
                                 'reason' => RECOVER_EMAIL_DB_ERROR);
                }
            } else {
                $ret = array('result' => 0,
                             'reason' => RECOVER_EMAIL_NOT_FOUND);
            }
        } else {
            $ret = array('result' => 0,
                         'reason' => RECOVER_NO_EMAIL);
        }
        
        echo json_encode($ret);
    }
    
    public function _check_recovery_code() {
        $dat = $this->Command->getData();
        
        if ($dat->code) {
            $code = $this->db->select("password_recoveries")->where("code = '".$dat->code."'")->single();
        
            if ($code->code) {
                // check if it's valid
                $c2 = $this->db->select("password_recoveries")->where(["code = '".$dat->code."'", "expires > NOW()"], "and")->single();
            
                if ($c2->code) {
                    if ((int)$c2->recovered == 1) {
                        $ret = array('result' => 0,
                                     'reason' => 2);
                    } else {
                        $ret = array('result' => 1,
                                     'authCode' => $this->gen_auth_code($code->user_id));
                    }
                } else {
                    $ret = array('result' => 0,
                                 'reason' => 1);
                }
            } else {
                $ret = array('result' => 0,
                             'reason' => 0);
            } 
        } else {
            $ret = array('result' => 0,
                         'reason' => 0);
        }
        
        echo json_encode($ret);
    }
    
    public function _reset_password()
    {
        $dat = $this->Command->getData();
        
        if ($dat->password) {
            $user = $this->get_user();
            
            if ($user->id) {
               if ($this->db->update("users")->set("password = '".md5($dat->password)."'")->where("id = '".$user->id."'")->go()) {
                    $ret = array('result' => 1);   
                   
                 //  // nuke auth code
                   $this->db->delete("auth_codes")->where("code = '".$dat->authCode."'")->go();
                   
                   // set code as used
                   $this->db->update("password_recoveries")->set("recovered = '1'")->where("code = '".$dat->recovery_code."'")->go();
               } else {
                    $ret = array('result' => 0,
                                 'reason' => RESET_DB_ERROR);
               }
                
            } else {
                $ret = array('result' => 0,
                             'reason' => RESET_NO_USER);
            }
        } else {
            $ret = array('result' => 0,
                         'reason' => RESET_PASSWORD_NO_PASSWORD);
        }
        
        echo json_encode($ret);
    }
    
    
    
    public function _test()
    {
        $dat = $this->Command->getData();
       
        $ret = array('authCode' => $dat->authCode,
            'logged' => $this->logged()
        );
        
        echo json_encode($ret);
    }
    
    
    public function _register()
    {
        $dat = $this->Command->getData();
        
        // blacklist array tells which parameters not to add to the db
        $blacklist = array();
        $blacklist[]= "error_msg";
        $blacklist[]= "error";
        
        $this->db->insert("users");
        
        // manually encrypt password
        if ($dat->password) {
            $dat->password = md5($dat->password);
        }
        
        $dat->permissions = DEFAULT_PERMISSIONS;
        
        // add passed variables in the object
        if ($dat) {
            foreach ($dat as $key => $value) { 
                if (!in_array($key, $blacklist) && !is_numeric($key)) {
                    $this->db->pair($key, $value);   
                }
            }
        }
        
        $q = $this->db->getQuery();
    
        if (strpos($q, 'VALUES') > 0) {
            if ($this->db->go()) {
           
                // get id
                $id = $this->db->lastId();
            
                // get auth code
        
                $dat = (array)$dat;
                $dat['authCode'] = $this->gen_auth_code($id);
                $dat = (object)$dat;
            
                $ret = array('result' => 1,
                             'user' => $dat);            
            } else {
                $ret = array('result' => 0,
                             'reason' => REGISTER_ERROR_DB,
                            'query' => $q);
            }
        } else {
            $ret = array('result' => 0,
                         'reason' => REGISTER_NO_DATA);
        }
       
        echo json_encode($ret);
    }
    
    public function _check_auth_code()
    {
        $dat = $this->Command->getData();
        
        $code = $dat->authCode;
        
        $date = new DateTime();
        
        $check = $this->db->select("auth_codes")->what("count(*) as count, expires, code, NOW()")->where("code = '$code'")->single();
        
        if ($check->code) {
            // check if code has expired
            $exp = new DateTime($check->expires);
            
            if ($exp > $date) {
                // hasn't expired; 
                // should lifespan be pushed forward?
                if (PUSH_AUTHCODE_LIFESPAN) {
                    // yes
                    $new_expiration = new DateTime();
                    $new_expiration->add(new DateInterval('PT'.AUTHCODE_LIFESPAN.'S'));
                    $this->db->update("auth_codes")->set("expires = '".$new_expiration->format('Y-m-d H:i:s')."'")->set("created = created")->where("code = '$code'");
                    $q = $this->db->getQuery();
                    $this->db->go();
                    
                    $ret = array('result' => 1,
                                 'query' => $q,
                                 'user' => $this->strip_sensitive($this->get_user()));
                } else {
                    // no
                    $date->add(new DateInterval('PT'.AUTHCODE_CUTOFF.'S'));
                
                    if ($exp > $date) {
                        // auth token is still good
                        // get user object        
                    
                        $ret = array('result' => 1,
                                     'user' => $this->strip_sensitive($this->get_user()));           
                    } else {
                        // has expired
                        $this->expireAuthToken();
                        $ret = array('result' => 0,
                                     'reason' => EXPIRED_AUTHCODE); 
                    }
                }
                    
                $ret = array('result' => 1,
                                 'user' => $this->strip_sensitive($this->get_user()));           
            } else if ($exp <= $date) {
                // has expired
                $this->expireAuthToken();
                $ret = array('result' => 0,
                             'reason' => EXPIRED_AUTHCODE);
            }
        } else {
            // no active auth code found
            $this->expireAuthToken();
            
            // try to delete the provided auth code from the system, if it exists
            $ret = array('result' => 0,
                         'reason' => NO_AUTHCODE);
        }
        echo json_encode($ret);
    }
        
    private function gen_auth_code($id) 
    {
        $code = get_random_string(); 
        
        // ensure this auth code isn't currently in the db
        // Which is literally a 1 in 7.679eX63 chance.
        while ($this->db->select("auth_codes")->what("code")->where("code = '$code'")->getCount() > 0) {
            $code = get_random_string();
        }
        
        // determine expiration of code
        $date = new DateTime();
        $date->add(new DateInterval('PT'.AUTHCODE_LIFESPAN.'S'));

        // add to db
        $added_code = $this->db->insert("auth_codes")
             ->pair("code", $code)
             ->pair("user_id", $id)
             ->pair("created", "NOW()")
             ->pair("expires", $date->format('Y-m-d H:i:s'))->go();
        
        if ($added_code) {
            return $code;
        } else { 
            return false;
        }
    }
    
    private function expireAuthToken()
    {
        $dat = $this->Command->getData();
        return $this->db->delete("auth_codes")->where("code = '".$dat->authCode."'")->getQuery();
    }
    
    public function strip_sensitive($o) 
    {
        unset($o->password);
        unset($o->id);
        unset($o->user);
        unset($o->error);
        unset($o->error_msg); 
        
        return $o;
    }
    
    public function _check_unique() 
    {
        $dat = $this->Command->getData();
     
        if ($dat->type) { 
            
            // strip out the first bit
            
            $dat->type = substr($dat->type, (strpos($dat->type, ".")+1), strlen($dat->type));
            $q = $this->db->select("users")->what("COUNT(*) as count")->where($dat->type." = '".$dat->val."'");
            if ($q->getCount() > 0) {
                // not unique
                $ret = array('isUnique' => false, 'query' => $q->getQuery());
            
            } else {
                // unique
                $ret = array('isUnique' => true, 'query' => $dat->type . "|" .$dat->val."|");
            }
        } else {
            $ret = array('isUnique' => false);
        }
        echo json_encode($ret);   
    }
}      
 
?>

