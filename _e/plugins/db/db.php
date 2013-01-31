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
            $this->e->_messaging->addMessage('Unable to connect to database server');
            $return = FALSE;
        } else {
            $return = mysql_select_db($this->e->_config->get('mysql.database'),$this->db);
            if (!($return)){
                $this->e->_messaging->addMessage('Unable to select database schema');
            } else {
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
            $return = mysql_query($SQL,$this->db);
            $this->disconnect();
        } else {
            $return = FALSE;
        }
        return $return;
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
    
}
?>
