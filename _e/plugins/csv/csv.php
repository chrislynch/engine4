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
        $return = array('_records' => array());
        $csvfile = fopen($file,'r');
        if ($csvfile){
            $fields = fgetcsv($csvfile);
            foreach($fields as $field){
                $return[$field] = array();
            }
            while($record = fgetcsv($csvfile)){
                $newrecord = array();
                for ($index = 0; $index < count($record); $index++) {
                    $newrecord[$fields[$index]] = $record[$index];
                }
                foreach($fields as $field){
                    $return[$field][] = $newrecord;
                }
                $return['_records'] = $newrecord;
            }
        } else {
            // Send back a "FALSE" if we can't read the file
            $return = FALSE;
        }
        return $return;
    }
}
?>
