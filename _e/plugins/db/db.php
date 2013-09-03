<?php

class _db {
    
    private $e;
    private $db;
    
    public function __construct(&$e){
        $this->e =& $e;
        // DB requires messaging and config - load these plugins
        if(!isset($this->e->_config)){ $this->e->_loadPlugin('config'); }
        if(!isset($this->e->_messaging)){ $this->e->_loadPlugin('messaging'); }
    }
    
    private function connect(){
        
        $this->db = mysql_connect($this->e->_config->get('mysql.server'),
                                $this->e->_config->get('mysql.user'),
                                $this->e->_config->get('mysql.password'));
        if (!$this->db){
            $this->e->_messaging->addMessage('Unable to connect to database server',-9);
            $return = FALSE;
        } else {
            $return = mysql_select_db($this->e->_config->get('mysql.database'),$this->db);
            if (!($return)){
                $this->e->_messaging->addMessage('Unable to select database schema',-9);
            } else {
                /*
                $this->query("SET NAMES 'utf8';");
                 */
                /*
                $this->query("SET character_set_results = 'utf8', 
                              character_set_client = 'utf8', 
                              character_set_connection = 'utf8', 
                              character_set_database = 'utf8', 
                              character_set_server = 'utf8'");
                 
                 */
                mysql_set_charset('utf8',$this->db); 
                $return = TRUE;
            }
        }
        
        return $return;
    }
    
    private function disconnect(){
        mysql_close($this->db);
    }
    
    function select($SQL){
        // Run a select statement
        if ($this->connect()){
        	if(isset($_GET['debug'])){ print $SQL . "<br>"; }
            $return = mysql_query($SQL,$this->db);
            $this->disconnect();
        } else {
            $return = FALSE;
        }
        return $return;
    }
    
    function query($SQL){
        return $this->select($SQL);
    }
    
    function insert($SQL){
        if ($this->connect()){
            mysql_query($SQL,$this->db);
            $return = mysql_insert_id($this->db);
            $this->disconnect();
        } else {
            $return = FALSE;
        }
        return $return;
    }
    
    function update($SQL){
        // Run the delete command and return how many rows were affected
        if ($this->connect()){
            mysql_query($SQL,$this->db);
            $return = mysql_affected_rows($this->db);
            $this->disconnect();
        } else {
            $return = FALSE;
        }
        return $return;
    }
    
    function delete($SQL){
        // Run the delete command and return how many rows were affected
        return $this->update($SQL);
    }
    
    function assocarray($SQL,$PK = ''){
        $data = $this->select($SQL);
        $array = array();
        while($arrayitem = mysql_fetch_assoc($data)){
        	if (strlen($PK) > 0){
        		$array[$arrayitem[$PK]] = $arrayitem;
        	} else {
				$array[] = $arrayitem;
        	}
        }
        return $array;
    }
    
    function nonassocarray($SQL){
        $data = $this->select($SQL);
        $array = array();
        while($arrayitem = mysql_fetch_array($data)){
            $array[] = $arrayitem;
        }
        return $array;
    }
    
    function result($SQL,$row = 0,$field = 0){
        $data = $this->select($SQL);
        return mysql_result($data, $row, $field);
    }
    
    function escape($string){
        return mysql_real_escape_string($string,$db);
    }
    
}
?>
