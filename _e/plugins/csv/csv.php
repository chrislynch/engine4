<?php

class _csv{
    
    public function __construct(&$e){
        $this->e =& $e;
    }
    
    public static function loadCSV($file){
        // Load a CSV file into an associative array and return it
        $return = array();
        $csvfile = fopen($file,'r');
        if ($csvfile){
            $fields = fgetcsv($csvfile);
            while($record = fgetcsv($csvfile)){
                $newrecord = array();
                for ($index = 0; $index < count($record); $index++) {
                    $newrecord[$fields[$index]] = $record[$index];
                }
                $return[] = $newrecord;
            }
        } else {
            // Send back a "FALSE" if we can't read the file
            $return = FALSE;
        }
        return $return;
    }
    
    public static function loadCSV2($file){
        // Load a CSV file into an array of associated arrays
        // Constructing a dataset that is easily searched and sorted
        $return = array('_records' => array(), '_fields' => array());
        $csvfile = fopen($file,'r');
        if ($csvfile){
            // Load our fields and remember what they are
            $fields = fgetcsv($csvfile);
            foreach($fields as $field){
                $return[$field] = array();
            }
            $return['_fields'] = $fields;
            
            while($record = fgetcsv($csvfile)){
                $newrecord = array();
                for ($index = 0; $index < count($record); $index++) {
                    $newrecord[$fields[$index]] = $record[$index];
                }
                foreach($fields as $field){
                    $return[$field][$newrecord[$field]][] = $newrecord;
                }
                $return['_records'][] = $newrecord;
            }
            
            foreach($fields as $field){
                ksort($return[$field]);
            }
            
        } else {
            // Send back a "FALSE" if we can't read the file
            $return = FALSE;
        }
        return $return;
    }
    
    public function filter($data,$filters = array()){
        // Filter one of our 2D CSV arrays
        if(!isset($data['_records']) OR !(isset($data['_fields']))){
            // This does not look like one of our 2D data sets
            return FALSE;
        } else {
            // Crack on a filter that bad boy!
            $return = array('_records' => array(), '_fields' => $data['_fields']);
            foreach($data['_records'] as $record){
                $add = TRUE;
                foreach($filters as $filterKey => $filterValue){
                    if (!($filterKey == 'q')){ // Automatically ignore "q" in case $filters is really $_GET
                        if(strlen($filterValue) > 0){
                            if (strtolower($record[$filterKey]) !== strtolower($filterValue)){
                                // Note - comparisons as case insensitive
                                $add = FALSE;
                            }
                        }
                    }
                }
                if ($add){
                    $return['_records'][] = $record;
                }
            }
            return $return;
        }
    }
    
    public function import($file,$table){
    	global $e;
    	$return = 0;
    	// This only works if a database exists and that we have configuration for it
    	// Don't try to load the plugin here, just check that we can connect
    	if ($e->_db->OK()){
    		// Get the new table set up
    		$e->_db->query("DROP TABLE $table");
    		$e->_db->query("CREATE TABLE `$table` (
                			`Imported_ID` int(11) NOT NULL AUTO_INCREMENT,
                			PRIMARY KEY (`Imported_ID`)
                			) ENGINE=InnoDB DEFAULT CHARSET=latin1");
    		// Open the CSV file
    		$csv = fopen($file,'r');
    		
    		// Pick up the fields from the header
    		$fields = fgetcsv($csv);
    		$dbfields = array();
    		
    		// Add the fields to the table
    		foreach($fields as $field){
    			if(!isset($dbfields[$field])){
    				if(strlen($field) > 0){
    					$e->_db->query("ALTER TABLE `$table` ADD COLUMN `$field` VARCHAR(1024) NULL;");
    					$dbfields[$field] = $field;
    				}
    			}
    		}
    		    		
    		// Add each row to the table
    		while($row = fgetcsv($csv)){
    			$SQL = "INSERT INTO $table SET ";
    			$SQLFields = array();
    			for ($index = 0; $index < count($fields); $index++) {
    				if(!isset($SQLFields[$fields[$index]])){
    					if (strlen($fields[$index]) > 0){
    						$SQL .= '`' . $fields[$index] . '` = "' . $e->_db->escape($row[$index]) . '",' . "\n";
    						$SQLFields[$fields[$index]] = $fields[$index];
    					}
    				}
    			}
    			$SQL .= 'Imported_ID = 0;';
    			$e->_db->insert($SQL);
    			print "<li>$SQL</li>";
				$return ++;
    		}	
    	}
    	
    	return $return; // Return the number of rows that we were able to insert.
	}
    
}
?>
