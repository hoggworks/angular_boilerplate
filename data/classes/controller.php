<?php
class Controller {
    var $Command;
    var $db;
    var $email;
    var $l;
    var $a;
    var $q;
    var $password_required;
    var $admin_required;
    
    function Controller(&$command, &$e = null)
    {
        if ($e) {
            $this->email = $e;
        }
        
        $this->Command = $command;    
    }
 
    function _default()
    {
 
    }
    
    function _error()
    {
        echo "Unable to process " . $this->Command->getFunction()."<br/>";
    }
    
    function _no_auth() {
        $ret = array('result' => 0,
                     'status' => UNAUTHORIZED);
        
        echo json_encode($ret);    
    }
    
    function get_user() 
    {
        $dat = $this->Command->getData();
        if ($dat->authCode) {
            return $this->db->select("auth_codes as a")
            ->where(["a.code = '".$dat->authCode."'", "a.expires > NOW()"], "and")
            ->leftJoin("users as u on u.id = a.user_id")->single();
        } else {
            return false;
        } 
    }
    
    // get functions for logged in status and admin status
    function logged() { return $this->l; }
    function admin() { return $this->a; }
    
    function execute($db)
    {
        $this->db = $db;
        
        $functionToCall = $this->Command->getFunction();
        
         
        // determine if user is logged in
        $dat = $this->Command->getData();
        if ($dat) {
            if (array_key_exists('authCode', $dat)) {
                // check if authcode is valid
                if ($this->db->select("auth_codes")->where("code = '".$dat->authCode."' and expires > NOW()")->getCount() > 0) {
                    // user is logged in
                    $this->l = true;
            
                    // determine if user is an admin
                    $user = $db->select("auth_codes as a")
                    ->where("a.code = '".$dat->authCode."'")
                    ->leftJoin("users as u on u.id = a.user_id")->single();
            
                    if ($user->permissions == PERMISSIONS_ADMIN) {
                        $this->a = true;
                    } else {
                        $this->a = false;
                    }
                } else {
                    $this->l = false;
                }
            } else {
                $this->l = false;
            }
        } else {
            $this->l = false;
        }
        
        // compare function to list of 
        // 'password required' pages.
         
        if (in_array($functionToCall, $this->password_required)) {
            // desired page requires authentication
            // does authentication exist?
            if ($this->logged()) {
                // proceed as planned
            } else {
                // redirect call to the no_auth screen
                $functionToCall = 'no_auth';
            }
        } 
        
        if (in_array($functionToCall, $this->admin_required)) {
            // desired page requires authentication + admin
            // admin overrides authentication (as admin implicitly requires
            // authentication
            if ($this->admin()) {
                // proceed as planned
            } else {
                // redirect call to no_auth screen
                $functionToCall = 'no_auth';
            }
        }
                
            
        if($functionToCall == '')
        {
            $functionToCall = 'default';
        }
 
        if(!is_callable(array(&$this,'_'.$functionToCall)))
        {
            $functionToCall = 'error';
        }
 
        call_user_func(array(&$this,'_'.$functionToCall));
    }
    
    function saveTransaction($type, $user_id, $from, $to) {
        // this method dumbly saves data passed to it into a transactions table
        $save = $this->db->insert("transactions")
            ->pair("type", $type)
            ->pair("user_id", $user_id)
            ->pair("from", $from)
            ->pair("to", $to)
            ->go();
        
        
        if (!$save) {
            // save failed; attempt to set an error to that effect
            
        }
        return $save;   
    }
}
?>