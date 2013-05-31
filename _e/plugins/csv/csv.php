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
}
?>
