<?php

class statController extends Controller
{
    public $password_required = array();
    public $admin_required = array();
    
    function _default()
    {

    }   
    
    function _add() {
        $dat = $this->Command->getData();
        $u = $this->get_user()->id;
        
        if (!$u) {
            $u = '';
        }
       
        if (isset($dat->type)) {        
            $added = $this->db->insert("stats")
            ->pair("type", $dat->type)
            ->pair("happened", "NOW()")
            ->pair("user_id", $u)
            ->pair("controller", $dat->controller)
            ->pair("msg", $dat->msg)
            ->go();
            
            if ($added) {
                $ret = array('result' => 1);
            } else {
                $ret = array('result' => 0);
            }
        } else {
            $ret = array('result' => 0);    
        }
        
        echo json_encode($ret);
    }
}
    
?>