<?php

class _messaging {
    
    var $messages = array();
    
    private $e;
        
    public function __construct(&$e){
        $this->e =& $e;
        if(!isset($this->e->_config)){ $this->e->_loadPlugin('config'); }
        if(isset($_SESSION['e4_messages'])) { $this->messages = $_SESSION['e4_messages']; }
    }
    
    function addMessage($message,$type = 0){
        if ($type == -9){
            die($message);
        } else {
            $this->messages[$message] = array('message'=>$message,'type'=>$type);
            $_SESSION['e4_messages'] = $this->messages;
        }
    }
    
    function HTMLMessages($clear = TRUE){
    	$HTML = "<div id='messages'>";
    	foreach($this->messages as $message){
    		$HTML .= "<div class='message {$message['type']}'>{$message['message']}</div>";
    	}
    	$HTML .= "</div>";
    	$this->clearMessages();
    	return $HTML;
    }
    
    function clearMessages(){
    	$this->messages = array();
    	unset($_SESSION['e4_messages']);
    }
    
    function sendMessage($toAddress,$toName,$subject,$message,$fromAddress = '', $fromName = '', $replyAddress = '', $replyName = '', $private = FALSE){
        require_once('_e/lib/PHPMailer/class.phpmailer.php');
        //include("class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded

        $mail = new PHPMailer(true); // the true param means it will throw exceptions on errors, which we need to catch

        $mail->IsSMTP(); // telling the class to use SMTP

        try {
            $mail->Host       = $this->e->_config->get('smtp.host'); // "mail.yourdomain.com"; // SMTP server
            $mail->SMTPDebug  = 0;                              // 2; // enables SMTP debug information (for testing)
            $mail->SMTPAuth   = true;                           // enable SMTP authentication
            $mail->Port       = $this->e->_config->get('smtp.port'); //26; // set the SMTP port for the GMAIL server
            $mail->Username   = $this->e->_config->get('smtp.user'); // "yourname@yourdomain"; // SMTP account username
            $mail->Password   = $this->e->_config->get('smtp.password'); // "yourpassword";        // SMTP account password
            
            // To
            $mail->AddAddress($toAddress, $toName);
            
            // From
            if (strlen($fromAddress) == 0){ $fromAddress = $this->e->_config->get('smtp.from.Address'); }
            if (strlen($fromName) == 0){ $fromName = $this->e->_config->get('smtp.from.Name'); }
            if (strlen($replyAddress) == 0){ $replyAddress = $this->e->_config->get('smtp.reply.Address'); }
            if (strlen($replyName) == 0){ $replyName = $this->e->_config->get('smtp.reply.Name'); }
            
            $mail->SetFrom($fromAddress, $fromName);
        	if(strlen($replyAddress) > 0 AND strlen($replyName) > 0){
            	$mail->AddReplyTo($replyAddress, $replyName);
            }
            
            // Subject and Message
            $mail->Subject = $subject;
            $mail->MsgHTML($message);
            // $mail->AltBody = 'To view the message, please use an HTML compatible email viewer!'; // optional - MsgHTML will create an alternate automatically
            
            // Attachments
            /*
            $mail->AddAttachment('images/phpmailer.gif');      // attachment
            $mail->AddAttachment('images/phpmailer_mini.gif'); // attachment
            */
            
            $mail->Send();
            if (!$private){$this->addMessage("email sent to $toAddress");}
            return TRUE;
            
        } catch (phpmailerException $e) {
            if (!$private){$this->addMessage("Unable to send email to $toAddress. eMail system said " . $e->errorMessage(),'error');} 
            return FALSE;
        }
    }
}

