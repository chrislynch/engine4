<?php

class _forms {
    
    private $e;
    private $db;
    
    public function __construct(&$e){
        $this->e =& $e;
        // DB requires messaging and config - load these plugins
        
    }
    
    public function formToEmail($to,$subject){
        if(!isset($this->e->_config)){ $this->e->_loadPlugin('config'); }
        if(!isset($this->e->_messaging)){ $this->e->_loadPlugin('messaging'); }
        
        $toAddress = $to;
	$toName = $to;
	$subject = $subject;
	
        $message = "<h1>$subject</h1><table>";
	$message .= '<tr><td colspan="2"><strong>POST Data</strong></td></tr>';
	foreach($_POST as $key => $value){
            if($key!=='submit'){
                    $message .= "<tr><td>$key:&nbsp;</td><td>$value</td></tr>";
            }
	}
        $message .= '</table><hr><h2>Additional Information</h2><table><tr><td colspan="2"><strong>GET Data</strong></td></tr>';
	foreach($_GET as $key => $value){
            if($key!=='submit'){
                    $message .= "<tr><td>$key:&nbsp;</td><td>$value</td></tr>";
            }
	}
        $message .= '<tr><td colspan="2"><strong>REQUEST Data</strong></td></tr>';
	foreach($_REQUEST as $key => $value){
            if($key!=='submit'){
                    $message .= "<tr><td>$key:&nbsp;</td><td>$value</td></tr>";
            }
	}
        $message .= '<tr><td colspan="2"><strong>SERVER Data</strong></td></tr>';
	foreach($_SERVER as $key => $value){
            if($key!=='submit'){
                    $message .= "<tr><td>$key:&nbsp;</td><td>$value</td></tr>";
            }
	}
        $message .= '<tr><td colspan="2"><strong>SESSION Data</strong></td></tr>';
	foreach($_SESSION as $key => $value){
            if($key!=='submit'){
                    $message .= "<tr><td>$key:&nbsp;</td><td>$value</td></tr>";
            }
	}
        $message .= '<tr><td colspan="2"><strong>ENV Data</strong></td></tr>';
	foreach($_ENV as $key => $value){
            if($key!=='submit'){
                    $message .= "<tr><td>$key:&nbsp;</td><td>$value</td></tr>";
            }
	}
	
	$this->e->_loadPlugin('messaging');
	$this->e->_messaging->sendMessage($toAddress,$toName,$subject,$message);
        
    }
    
}
?>
