<?php
class logController extends Controller
{
    public $password_required = array();
    public $admin_required = array();
    
    function _default()
    {
        $dat = $this->Command->getData();
        
        $u = $this->get_user()->id;
        
        $dat->happened = date("Y-m-d ") . $dat->happened;
        // this takes all logs and saves as appropriate
        // selectively save info, warn and error based on variables
        $this->db->insert("logs")
            ->pair("type", $dat->type)
            ->pair("msg", $dat->msg)
            ->pair("happened", $dat->happened)
            ->pair("controller", $dat->context)
            ->pair("action", $dat->action)
            ->pair("user_id", $u)
            ->pair("data", json_encode($dat->data))
            ->go();
        
        $last_id = $this->db->lastId();
        
        switch ($dat->type) {
            case "info":
                if (EMAIL_ON_INFO) {
                    $this->emailLog($id);   
                }
                break;
            case "warn":
                if (EMAIL_ON_WARN) {
                    $this->emailLog($id);
                }
                break;
            case "error":
                if (EMAIL_ON_ERROR) {
                    $this->emailLog($id);
                }
                break;
        }
        
        $ret = array('happened' => $dat->happened, 'u' => $u);
        echo json_encode($ret);
    }
    
    function emailLog()
    {
        $dat = $this->Command->getData();
        $u = $this->get_user()->id;
        
        $obj = array('type' => 'site_'.$dat->type,
                     'params' => array( 'msg' => $dat->msg,
                                        'happened' => $dat->happened,
                                        'controller' => $dat->context,
                                        'action' => $dat->action,
                                        'data' => json_encode($dat->data),
                                        'user_id' => $u,
                                        'site' => SITE_NAME)
                    );
        $this->email->_send($obj);
    }
    
}

?>

