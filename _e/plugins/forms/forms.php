<?php

class _forms {
    
    private $e;
    private $db;
    
    public function __construct(&$e){
        $this->e =& $e;
        // DB requires messaging and config - load these plugins
    }
    
    public function formToDB($tablename){
        // Create the tables if they do not exist
        $this->e->_loadPlugin('db');
        $this->e->_db->query("CREATE  TABLE IF NOT EXISTS `$tablename` (
                                `ID` INT NOT NULL AUTO_INCREMENT ,
                                `Timestamp` TIMESTAMP NULL ,
                                PRIMARY KEY (`ID`) );");
        
        $this->e->_db->query("CREATE  TABLE IF NOT EXISTS `{$tablename}_data` (
                                `ID` INT NOT NULL AUTO_INCREMENT ,
                                `method` VARCHAR(45) NULL ,
                                `name` VARCHAR(255) NULL ,
                                `value` TEXT NULL ,
                                PRIMARY KEY (`ID`,`method`,`name`) );");
                
        // Create the header for the current item
        $ID = $this->e->_db->insert("INSERT INTO `$tablename` SET ID = 0");
        
        // Create the detail requests
        $this->globaltoDB($_POST, 'POST', $tablename, $ID);
        $this->globaltoDB($_GET, 'GET', $tablename, $ID);
        $this->globaltoDB($_REQUEST, 'REQUEST', $tablename, $ID);
        $this->globaltoDB($_SERVER, 'SERVER', $tablename, $ID);
    }
    
    private function globaltoDB($global,$globalname,$tablename,$ID){
        foreach($global as $key => $value){
            $this->e->_db->insert("INSERT INTO `{$tablename}_data` SET ID=$ID, method='$globalname', name = '$key', value = '" . mysql_real_escape_string($value) . "';" );
        }
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
