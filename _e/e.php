<?php

/*
 * 
 */

class e {
    
    function go() {
        
        // Get initial list of directories
        $directories = scandir('.');
        
        foreach($directories as $directory){
            // Work through directories, processing each in order.
            if ($this->isValidDirectory($directory)){
                $this->open($directory);
            }
        }
        
        print 'Hello World';
    }
    
    private function find($inDirectory){
        if (isset($_REQUEST['q'])){
            $q = $_REQUEST['q'];
        } else {
            $q = '';
        }
        
        while(strlen($q) > 0){
            print "Looking for $q in $inDirectory<br>";
            if (file_exists($inDirectory . '/' . $q)){
                if ($this->isValidDirectory($inDirectory . '/' . $q)){
                    $this->open($inDirectory . '/' . $q);
                }
            } else {
                $q = $this->dirup($q);
            }
        }
    }
    
    private function dirup($directory){
        $directory = explode('/',$directory);
        array_pop($directory);
        $directory = implode('/',$directory);
        return $directory;
    }
    
    private function isValidDirectory($directory){
        $return = is_dir($directory);
        if ($return){
            if ($directory == '.') { $return = FALSE; }
            if ($directory == '..') { $return = FALSE; }
            if (substr($directory,0,1) == '.') { $return = FALSE; }
            if (substr($directory,0,1) == '_') { $return = FALSE; }
        }
        return $return;
    }
}

?>
