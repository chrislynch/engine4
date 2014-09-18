<?php
class _db {
    
    private $e;
    private $db;
    
    public function __construct(&$e){
        $this->e =& $e;
        // DB requires messaging and config - load these plugins
        // if(!isset($this->e->_config)){ $this->e->_loadPlugin('config'); }
        // if(!isset($this->e->_messaging)){ $this->e->_loadPlugin('messaging'); }
    }
    
    private function connect(){
	
    	if ($this->e->_config->get('mysql.server') !== ''){
    		
    		$this->db = mysql_connect($this->e->_config->get('mysql.server'),
    				$this->e->_config->get('mysql.user'),
    				$this->e->_config->get('mysql.password'),
    				TRUE); // Send new_link = TRUE to avoid stealing someone else's connection to the DB.
    		
    		if (!$this->db){
    			$this->e->_messaging->addMessage('Unable to connect to database server',-9);
    			$return = FALSE;
    		} else {
    			$return = mysql_select_db($this->e->_config->get('mysql.database'),$this->db);
    			if (!($return)){
    				$this->e->_messaging->addMessage('Unable to select database schema ' . $this->e->_config->get('mysql.database'),-9);
    			} else {
    				if (function_exists('mysql_set_charset') == TRUE){
    					mysql_set_charset('utf8',$this->db);
    				}
    				$return = TRUE;
    			}
    		}
    		
    		return $return;
    	} else { 
    		$this->db = new PDO("sqlite:_custom/_default/data/f.db");
    		return TRUE;
    	} 
	
    }
    
    private function disconnect(){
        $this->db = NULL;
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
            if ($this->e->_config->get('mysql.server') !== ''){
            	$return = mysql_query($SQL,$this->db);
            } else {
            	$return = $this->db->query($SQL);
            }
            $this->disconnect();
        } else {
            $return = FALSE;
        }
        return $return;
    }
    
    function query($SQL){
        return $this->select($SQL);
    }
    
    function insert($SQL,$Values = array()){
        if ($this->connect()){
	    $cmd = $this->db->prepare($SQL);
            $cmd->execute($Values);
            $return = $this->db->lastInsertId();
            $this->disconnect();
        } else {
            $return = FALSE;
        }
        return $return;
    }
    
    function insertinto($table,$args){
    	$functions = array('UNIX_TIMESTAMP()');

		$SQL = "INSERT INTO $table";
		$fields = array();
		$placeholders = array();
		$values = array();

    	foreach($args as $field => $value){
		$fields[] = $field;
		if(is_numeric($value)){
			$placeholders[] = $value;
		} else {
			// Check if the field is a function
			if(in_array($value, $functions)){
				$placeholders[] = $value;
			} else {
				$placeholders[] = ":$field";
				$values[":$field"] = $value;
			}
		}
    	}
		$SQL .= "(" . implode(',',$fields) . ") VALUES(" . implode(',',$placeholders) . ")";

    	if(TRUE){ print "$SQL<br>" . print_r($args,TRUE) . '<br>'; }
    	$return = $this->insert($SQL,$values); 
    	// print "Returning $return";
    	return $return;
    }
    
    function replaceinto($table,$args){
    	$functions = array('UNIX_TIMESTAMP()');

		$SQL = "REPLACE INTO $table";
		$fields = array();
		$placeholders = array();
		$values = array();

    	foreach($args as $field => $value){
		$fields[] = $field;
		if(is_numeric($value)){
			$placeholders[] = $value;
		} else {
			// Check if the field is a function
			if(in_array($value, $functions)){
				$placeholders[] = $value;
			} else {
				$placeholders[] = ":$field";
				$values[":$field"] = $value;
			}
		}
    	}
	$SQL .= "(" . implode(',',$fields) . ") VALUES(" . implode(',',$placeholders) . ")";

    	if(TRUE){ print "$SQL<br>" . print_r($args,TRUE) . '<br>'; }
    	$return = $this->insert($SQL,$values); 
    	// print "Returning $return<br>";
    	return $return;
    }
    
    function update($SQL){
        // Run the update (or delete) command and return how many rows were affected
        if ($this->connect()){
            $update = $this->db->prepare($SQL);
	    $update->execute();
            $return = $update->rowCount();
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
    
    /*
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
    */

    function result($SQL){
        $data = $this->select($SQL);
	return $data->fetchColumn();        
    }

    function escape($string){
	$this->connect();
	$return = $this->db->quote($string);
	$this->disconnect();
	/*
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
	*/
    	return $return;
    }
    
    /* Full text indexing and searching functions */

    /*
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
    */
}
?>
