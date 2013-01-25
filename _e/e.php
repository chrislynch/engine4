<?php

if (!function_exists('Markdown')) { include_once('_e/lib/phpmarkdownextra/markdown.php'); }

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
            print "<html><head><title>engine4</title></head><body><h1>engine4</h1><p>You are running engine4</p></body></html>";
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
                switch(strtolower(@$filearray[1])){
                    case '':
                        // Someone created a file without an extension.
                        // Ignore this silly file
                    case 'inc':
                        // Include a file, but buffer the output and put it into $e
                        ob_start();
                        include($directory . '/' . $file);
                        $this->$directoryarray[0]->html = ob_get_contents();
                        ob_end_clean();
                        break;
                        
                    case 'php':
                        // Run the PHP script. Run it here so that $this, in context, is $e
                        include($directory . '/' . $file);
                        break;
                    
                    case 'markdown': case 'md':
                    case 'txt': case 'text':
                    case 'htm': case 'html':
                        // Load up a recognised text file and mark it down
                        $this->$directoryarray[0]->$filearray[1] = file_get_contents($directory . '/' . $file);
                        $this->$directoryarray[0]->$filearray[1] = Markdown($this->$directoryarray[0]->$filearray[1]);
                        if (!isset($this->$directoryarray[0]->html)){ $this->$directoryarray[0]->html = $this->$directoryarray[0]->$filearray[1]; }
                        break;
                        
                    default:
                        // Check to see if the file is binary or text
                        if ($this->_isBinaryFile($directory . '/' . $file)){
                            if(!isset($this->$directoryarray[0]->_files)){ $this->$directoryarray[0]->_files = array(); }
                            // $this->$directoryarray[0]->$filearray[1] = $directory . '/' . $file;
                            $this->$directoryarray[0]->_files[$file] = $directory . '/' . $file;
                        } else {
                            $this->$directoryarray[0]->$filearray[1] = file_get_contents($directory . '/' . $file);
                        }
                }
            }
        }
        
        // Run the post-load event to make sure that everything is present and correct
        $this->$directoryarray[0]->_postload();
        
        return $return;
    }
    
    static function _dirup($directory){
        $directory = explode('/',$directory);
        array_pop($directory);
        $directory = implode('/',$directory);
        return $directory;
    }
    
    static function _search($path, $parameters = array()){
        $return = array();
        $results = scandir($path);
        foreach($results as $result){
            if (e::_isValidDirectory($path . '/' . $result,TRUE)){
                $returnItem = new e();
                $returnItem->_open($path . '/' . $result);
                $return[$result] = $returnItem;
            }
        }
        ksort($return);
        $return = array_reverse($return, TRUE);
        return $return;
    }
    
    static function _isValidDirectory($directory){
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
    
    static function _isValidFile($file,$directory){
        $return = TRUE;
        if (is_dir($directory . '/' . $file)) { $return = FALSE; }
        if ($file == '.') { $return = FALSE; }
        if ($file == '..') { $return = FALSE; }
        return $return;
    }
    
    static function _isBinaryFile($file){
        $binary_files = array('png','jpg','jpeg','gif','pdf');
        $file = explode('.',$file);
        $filetype = strtolower(array_pop($file));
        if(in_array($filetype, $binary_files)){
            return TRUE;
        } else {
            return FALSE;
        }
        /*
        if (function_exists('finfo_open') && file_exists($file)){
            $finfo = finfo_open(FILEINFO_MIME);
            $finfofiletype = substr(finfo_file($finfo,$file), 0, 4);
            if ($finfofiletype == 'text'){
                return FALSE;
            } else {
                return TRUE;
            }
        } else {
            return FALSE;
        }
         * 
         */
    }
    
    static function _domain() {
        return $_SERVER['SERVER_NAME'];
    }
    
    static function _basedir(){
        $indexphp = $_SERVER['SCRIPT_FILENAME'];
        $indexphp = explode('/',$indexphp);
        array_pop($indexphp);
        $indexphp = implode('/',$indexphp);
        $indexphp .= '/';
        return $indexphp;
    }
    
    static function _basehref(){
        $indexphp = $_SERVER['PHP_SELF'];
        $indexphp = explode('/',$indexphp);
        array_pop($indexphp);
        $indexphp = implode('/',$indexphp);
        $indexphp .= '/';
               
        return 'http://' . e::_domain() . $indexphp;
    }
    
    private function _loadplugin($plugin){
        // TODO: If we are running e in a subdirectory, we need to somehow tell it
        // so that it can still find plugins.
        require_once("_e/plugins/$plugin/$plugin.php");
        $pluginvar = '_' . $plugin;
        if (!isset($this->$pluginvar)){
            $this->$pluginvar = new $plugin();
        }
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
 
    function _load($file){
        // Load contents from a file into this object
    }
    
    function _postload() {
        // Look for variables in the html
        if (isset($this->html)){
            $this->extractVariables();
        }
        
        // Make image and file links work
        if (isset($this->html) && isset($this->_files)){
            foreach($this->_files as $filename => $filepath){
                $this->html = str_ireplace("'$filename'", "'$filepath'", $this->html);
                $this->html = str_ireplace("\"$filename\"", "\"$filepath\"", $this->html);

            }
        }
    }
    
    private function extractVariables(){
        $variableStart = FALSE;
        $variableEnd = FALSE;
        $loop = TRUE;
        
        while($loop == TRUE){
            $loop = FALSE;
            $variableStart = stripos($this->html,'{@');
            if (!($variableStart === FALSE)){
                $variableEnd = stripos($this->html,'@}');
                if (!($variableEnd === FALSE)){
                    // Grab the variable
                    $variable = substr($this->html, $variableStart, ($variableEnd - $variableStart) + 2);
                    // Expunge the variable from the html
                    $this->html = str_ireplace($variable, '', $this->html);
                    // Remove the delimiters from the variable
                    $variable = str_ireplace('{@', '', $variable);
                    $variable = str_ireplace('@}', '', $variable);
                    // Split the variable to name & value
                    $variable = explode('=',$variable);
                    $variablename = trim(array_shift($variable));
                    $variablevalue = trim(implode('=',$variable));
                    // Apply the value to the object
                    $this->variablename = $variablevalue;
                    // If we have found one, there may be more. Go and look for them.
                    $loop = TRUE;
                }

            }
        }
        
    }
    
}

?>
