<?php

$e = new e();
try {
    ob_start();                 // Start buffering output
    $e->_go();                  // Run e4
    $output = ob_get_contents();
    if (!$output){ print 'There was no output!'; }
    ob_end_clean();
    if (!$output){
        // There was no output. Print the default
        if (isset($e->content->html)){
            print $e->content->html;
        } else {
            // Print some default "Welcome to engine 4 guff"
        }
    } else {
        print $output;
    }
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

        // Create our new object
        $this->$directoryarray[0] = new eThing($directory);
        
        // Set the title of the item that we have found
        $title = $directoryarray[sizeof($directoryarray) - 1];
        // Take the date off the front of the directory, if it is there
        if (strstr($title,'.')){
            $title = explode('.',$title);    
            array_shift($title);
            $title = implode('.',$title);
        }
        $title = str_ireplace('-', ' ', $title);
        $title = ucwords($title);
        $this->$directoryarray[0]->title = $title;
        
        // Get the timestamp of the directory
        $this->$directoryarray[0]->timestamp = date ("F d Y H:i:s.", filemtime($directory));
                
        // Go and get the content files from the directory that we are opening
        $files = scandir($directory);
        
        foreach($files as $file){
            if ($this->_isValidFile($file,$directory)){
                // Set the return value to TRUE to acknowledge finding a file
                $return = TRUE;
                
                // Breakdown the file name that we find.
                $filearray = explode('.',$file);
                
                // Apply special loading routines depending on what content we find
                switch(strtolower($filearray[1])){
                    case 'php':
                        include($directory . '/' . $file);
                        break;
                    
                    case 'markdown':
                    case 'md':
                    case 'txt':
		    case 'text':
                        if (!function_exists('Markdown')) { include_once('_e/lib/phpmarkdownextra/markdown.php'); }
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
        
        // Run the post-load event to make sure that everything is present and correct
        $this->$directoryarray[0]->_postload();
        
        return $return;
    }
    
    private function _dirup($directory){
        $directory = explode('/',$directory);
        array_pop($directory);
        $directory = implode('/',$directory);
        return $directory;
    }
    
    protected function _search($path, $parameters = array()){
        $return = array();
        $results = scandir($path);
        foreach($results as $result){
            if ($this->_isValidDirectory($path . '/' . $result,TRUE)){
                $returnItem = new e();
                $returnItem->_open($path . '/' . $result);
                $return[] = $returnItem;
            }
        }
        return $return;
    }
    
    private function _isValidDirectory($directory){
        $return = is_dir($directory);
        if (strstr($directory,'/')){
            $directory = explode('/',$directory);
            $directory = array_pop($directory);
        }
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
    
    private function _loadplugin($plugin){
        require_once("_e/plugins/$plugin/$plugin.php");
        $pluginvar = '_' . $plugin;
        $this->$pluginvar = new $plugin();
    }
}

class eThing extends stdClass {
    
    var $path;
    var $url;
    
    var $title;
    var $timestamp;
    
    function __construct($path) {
        $this->path = $path;
        
        $url = explode('/',$path);
        array_shift($url);
        $url = implode('/',$url);
        if (strlen($url) == 0) { $url = '/'; }
        $this->url = $url;
    }
 
    function _postload() {
        // Called after the content has been loaded
    }
    
}

?>
