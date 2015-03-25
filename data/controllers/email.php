<?php
class emailController extends Controller
{
    public $password_required = array();
    public $admin_required = array();
    
    function _default()
    {
       // return "nothing here";
        //$email_file = "../emails/".$o->type.".html";
        //return $email_file;
        echo "ha";
    }
    
    function _send($obj = null)
    {
        
        
        // perform conversion of data object, if ne
        if ($obj == null) {
            // this method is being called from the front end
            $o = $this->Command->getData();
            $was_object = false;
        } else {
            // this method is being called from another php class
            $o = $obj;
            $was_object = true;
        }
        
        $email_file = EMAIL_DIRECTORY.$o->type.EMAIL_TEMPLATE_SUFFIX;
        
     
        if (is_file($email_file)) {
            $message = file_get_contents($email_file);
            foreach($o->params as $key => $value) {
                $message = str_replace("<%".$key."%>", $value, $message);
            }
            
            $send_email = $this->sendEmail($message, REPLY_TO, $o->params->to, $o->params->subject);
            
            if ($send_email) {
                $ret = array('result' => 1, 'msg' => $message);
                
            } else {
                $ret = array('result' => 0,
                             'reason' => RECOVER_EMAIL_ERROR,
                            'msg' => $message);
            }
        } else {
            $ret = array('result' => 0,
                         'reason' => EMAIL_TEMPLATE_NOT_FOUND,
                        'msg' => $email_file);
        }
    
     
        
        if ($was_object) {
            return (object)$ret;
        } else {
            echo json_encode($ret);
        }
    }
    
    private function sendEmail($content, $from, $to, $subject) {

		$boundary = rand(11111,99999);
		$boundary = 'multipart_boundary_'.md5($boundary);

		$headers = "From: <$from>\r\n";
    	$headers .= "Reply-To: <".REPLY_TO.">\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: multipart/mixed; boundary=$boundary"; 
        
        ob_start();

        ?>
        
        --<?php echo "$boundary\r\n"; ?>
Content-Type: text/plain; charset="utf-8"
Content-Transfer-Encoding: 7bit
<?php echo strip_tags($content); ?>
 
--<?php echo "$boundary\r\n"; ?>
Content-Type: text/html; charset="utf-8"
Content-Transfer-Encoding: 7bit
<?php echo $content; ?>
 
--<?php echo "$boundary\r\n"; ?>
 
<?php
$message = ob_get_contents();
ob_end_clean();
       
        $additional = "-f ".REPLY_TO; 

        $send_mail = mail($to,$subject,$message,$headers);
      // $send_mail = mail($to,$subject,$content,"", "");
        
        return $send_mail;
       }    
    
    
}
        
 
?>

