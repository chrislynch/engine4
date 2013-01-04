<?php

$e = new e();
try {
    $e->_go();    
} catch (Exception $exc) {
    echo $exc->getTraceAsString();
}

if (isset($_REQUEST['debug'])){ print '<pre>' . print_r($e,TRUE) . '</pre>'; }

class e {
    
    function _go() {
        
        // Get initial list of directories
        $directories = scandir('.');
        
        foreach($directories as $directory){
            // Work through directories, processing each in order.
            if ($this->_isValidDirectory($directory)){
                $this->_find($directory);
            }
        }
    }
    
    private function _find($inDirectory){
        $found = FALSE;
        
        if (isset($_REQUEST['q'])){
            $q = $_REQUEST['q'];
        } else {
            $q = '';
        }
        
        while(strlen($q) > 0 && !$found){
            if (file_exists($inDirectory . '/' . $q)){
                if ($this->_isValidDirectory($inDirectory . '/' . $q)){
                    if ($this->_open($inDirectory . '/' . $q)) {
                        $found = TRUE;
                    } else {
                        $q = $this->_dirup($q);
                    }
                }
            } else {
                $q = $this->_dirup($q);
            }
        }
        
        if (!$found){
            $this->_open($inDirectory);
        }
    }
    
    private function _open($directory){
        $return = FALSE;
        
        $directoryarray = explode('/',$directory);
        // Split the front off the first directory
        // This is in case we use numbers on our directories to order them
        if(strstr($directoryarray[0],'.')){
            $directoryarray[0] = explode('.',$directoryarray[0]);
            array_shift($directoryarray[0]);
            $directoryarray[0] = implode('.',$directoryarray[0]);
        }

        $this->$directoryarray[0] = new stdClass();
        
        $files = scandir($directory);
        
        foreach($files as $file){
            if ($this->_isValidFile($file,$directory)){
                $return = TRUE;
                $filearray = explode('.',$file);
                
                switch(strtolower($filearray[1])){
                    case 'php':
                        include($directory . '/' . $file);
                        break;
                    case 'markdown':
                    case 'md':
                    case 'txt':
		    case 'text':
                        include_once('_e/lib/phpmarkdownextra/markdown.php');
                        $this->$directoryarray[0]->$filearray[1] = file_get_contents($directory . '/' . $file);
                        $this->$directoryarray[0]->html = Markdown($this->$directoryarray[0]->$filearray[1]);
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
    
    private function _dirup($directory){
        $directory = explode('/',$directory);
        array_pop($directory);
        $directory = implode('/',$directory);
        return $directory;
    }
    
    private function _isValidDirectory($directory){
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
    
    private function _isValidFile($file,$directory){
        $return = TRUE;
        if (is_dir($directory . '/' . $file)) { $return = FALSE; }
        if ($file == '.') { $return = FALSE; }
        if ($file == '..') { $return = FALSE; }
        return $return;
    }
    
    private function _domain() {
        return $_SERVER['SERVER_NAME'];
    }
    
    private function _basedir(){
        $indexphp = $_SERVER['SCRIPT_FILENAME'];
        $indexphp = explode('/',$indexphp);
        array_pop($indexphp);
        $indexphp = implode('/',$indexphp);
        $indexphp .= '/';
        return $indexphp;
    }
    
    private function _basehref(){
        $indexphp = $_SERVER['PHP_SELF'];
        $indexphp = explode('/',$indexphp);
        array_pop($indexphp);
        $indexphp = implode('/',$indexphp);
        $indexphp .= '/';
               
        return 'http://' . $this->_domain() . $indexphp;
    }
}

?>
