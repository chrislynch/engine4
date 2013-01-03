<?php

$e = new e();
$e->go();

class e {
    
    function go() {
        
        // Get initial list of directories
        $directories = scandir('.');
        
        foreach($directories as $directory){
            // Work through directories, processing each in order.
            if ($this->isValidDirectory($directory)){
                $this->find($directory);
            }
        }
    }
    
    private function find($inDirectory){
        $found = FALSE;
        
        if (isset($_REQUEST['q'])){
            $q = $_REQUEST['q'];
        } else {
            $q = '';
        }
        
        while(strlen($q) > 0 && !$found){
            if (file_exists($inDirectory . '/' . $q)){
                if ($this->isValidDirectory($inDirectory . '/' . $q)){
                    if ($this->open($inDirectory . '/' . $q)) {
                        $found = TRUE;
                    } else {
                        $q = $this->dirup($q);
                    }
                }
            } else {
                $q = $this->dirup($q);
            }
        }
        
        if (!$found){
            $this->open($inDirectory);
        }
    }
    
    private function open($directory){
        $return = FALSE;
        $directoryarray = explode('/',$directory);
        $files = scandir($directory);
        
        foreach($files as $file){
            if ($this->isValidFile($file,$directory)){
                $return = TRUE;
                $filearray = explode('.',$file);
                $this->$directoryarray[0] = new stdClass();
                switch(strtolower($filearray[1])){
                    case 'php':
                        include($directory . '/' . $file);
                        break;
                    default:
                        // Check to see if the file is binary or text
                        $finfo = finfo_open(FILEINFO_MIME);
                        $finfofiletype = substr(finfo_file($finfo, $directory . '/' . $file), 0, 4);
                        if ($finfofiletype == 'text'){
                            $this->$directoryarray[0]->$filearray[1] = file_get_contents($directory . '/' . $file);
                        } else {
                            $this->$directoryarray[0]->$filearray[1] = $directory . '/' . $file;
                        }
                        
                }
                
            }
        }
        
        return $return;
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
            if ($directory == 'nbproject') { $return = FALSE; }
        }
        return $return;
    }
    
    private function isValidFile($file,$directory){
        $return = TRUE;
        if (is_dir($directory . '/' . $file)) { $return = FALSE; }
        if ($file == '.') { $return = FALSE; }
        if ($file == '..') { $return = FALSE; }
        return $return;
    }
}

?>
