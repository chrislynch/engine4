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
                $this->e->_messaging->addMessage('Unable to select database schema ' . $this->e->_config->get('mysql.database'),-9);
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
            	if (function_exists('mysql_set_charset') == TRUE){
            		mysql_set_charset('utf8',$this->db);
            	}
                
                $return = TRUE;
            }
        }
        
        return $return;
    }
    
    private function disconnect(){
        mysql_close($this->db);
    }
    
    function OK(){
    	// Check that we have a database by connecting and disconnecting
    	$return = FALSE;
    	if($this->connect()){
    		$this->disconnect();
    		$return = TRUE;
    	} else {
    		$return = FALSE;
    	}
    	return $return;
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
    
    function insertinto($table,$args,$PK,$debug=FALSE){
    	$functions = array('UNIX_TIMESTAMP()');
    	
    	// First field, normally the PK
    	if(is_numeric($args[$PK])){
    		$SQL = "INSERT INTO $table SET `$PK` = {$args[$PK]}";
    	} else {
    		$SQL = "INSERT INTO $table SET `$PK` = '". $this->escape($args[$PK]) . "'";
    	}
    	// Other fields.
    	foreach($args as $field => $value){
    		if($field !== $PK){
    			$SQL .= ", `$field` = ";
    			if(is_numeric($value)){
    				$SQL .= $value;
    			} else {
    				// Check if the field is a function
    				if(in_array($value, $functions)){
    					$SQL .= $value;
    				} else {
    					$SQL .= "'" . $this->escape($value) . "'";
    				}
    			}
    		}
    	}
    	if($debug){ print "$SQL<br>"; }
    	return $this->insert($SQL);
    }
    
    function replaceinto($table,$args,$PK){
    	$SQL = "REPLACE INTO $table SET $PK = {$args[$PK]}";
    	foreach($args as $field => $value){
    		if($field !== $PK){
    			$SQL .= ", $field = ";
    			if(is_numeric($value)){
    				$SQL .= $value;
    			} else {
    				if (strtoupper($value) == $value && substr($value,-2) == '()'){
    					// Looks like a function call
    					$SQL .= $value;
    				} else {
    					$SQL .= "'" . $this->escape($value) . "'";
    				}
    				
    			}
    		}
    	}
    	return $this->update($SQL);
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
    
    function keyarray($SQL,$PK){
        $data = $this->select($SQL);
        $array = array();
        while($arrayitem = mysql_fetch_assoc($data)){
            if (strlen($PK) > 0){
                $array[] = $arrayitem[$PK];
            } else {
                $array[] = array_shift($arrayitem);
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
    
    function result($SQL,$row = 0,$field = 0,$default = FALSE){
        $data = $this->select($SQL);
        if(mysql_num_rows($data) == 0){
        	return $default;
        } else {
        	return mysql_result($data, $row, $field);
        }
    }
    
    function escape($string){
    	$original = $string;
    	if($this->connect()){
    		if(! $string = mysql_real_escape_string($string,$this->db)){
    			$return = addslashes($original);
    		} else {
    			$return = $string;
    		}	
    	} else {
    		$return = addslashes($original);
    	}
    	return $return;
    }
    
    /* Full text indexing and searching functions */
    function fulltext_search($index,$keywords,$phrasemode = 0){
    
    	$SQL = 'SELECT 	ID, search_text,';
    	$SQL .= ' MATCH(s.search_text) AGAINST ("' . $keywords .'") as Relevance,';
    	$SQL .= ' MATCH(s.search_text) AGAINST ("' . $keywords .'" WITH QUERY EXPANSION) as Expanded_Relevance,';
    	$SQL .= ' MATCH(search_text) AGAINST ("""' . $keywords . '""" IN BOOLEAN MODE) as PhraseMatch, ';
        $SQL .= ' MATCH(search_text) AGAINST ("' . $keywords . '" IN BOOLEAN MODE) as BooleanORMatch, ';
        $keywords = explode(' ',$keywords);
        $keywords = '+' . implode(' +',$keywords);
        $SQL .= ' MATCH(search_text) AGAINST ("' . $keywords . '" IN BOOLEAN MODE) as BooleanANDMatch ';
    	$SQL .= ' FROM ' . $index . ' s ';
    	// Can remove or alter these clauses for broader searches
    	if($phrasemode == 1) { $SQL .= ' HAVING PhraseMatch = 1'; } else { $SQL .= ' HAVING BooleanORMatch > 0 '; }
    	// Don't change these unless you want irrelevant results first!
    	$SQL .= ' ORDER BY PhraseMatch DESC,BooleanANDMatch DESC,BooleanORMatch DESC,Relevance DESC,Expanded_Relevance DESC';
    
    	$results = $this->query($SQL);
    
    	return $results;
    }
    
    function fulltext_index_purge($index){
    	
    }
    
    function fulltext_index_populate($array){
    	
    }
    
    function fulltext_index_additem($item){
    	
    }
}
?>
