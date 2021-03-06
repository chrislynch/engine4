<?php

function _e_go(){
    global $e;
    global $argv;
    
    if(isset($argv[1])){
    	// We are being run from the command line. Parse out the arguments
    	$commandline_q = parse_url($argv[1]);
    	$_REQUEST['q1'] = $commandline_q['path'];
    	parse_str($commandline_q['query'],$commandline_p);
    	foreach($commandline_p as $key => $value){
    		$_REQUEST[$key] = $value;
    		$_GET[$key] = $value;
    		$_POST[$key] = $value;
    	}
    }
    
    switch(@$_REQUEST['q1']){
        /*
        case 'sitemap.xml':
            $e = new eSiteMap();
            print $e->xml();
            break;
        */
        /*
        case 'robots.txt':
            
            break;
        */
        default:
        	if (!function_exists('Markdown')) { include_once('_e/lib/phpmarkdownextra/markdown.php'); }
        	
            $starttheclock = microtime(TRUE);   

            if (isset($_REQUEST['debug'])){
                error_reporting(-1);
                ini_set('display_errors', 1);
            } else {
                error_reporting(0);	
                ini_set('display_errors', 0);
            }

            session_start();

            $e = new e();

            try {
            	if(isset($argv[1])){
            		$e->_go();					// Command line processes are not buffered
            	} else {
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
            	}
                
            } catch (Exception $exc) {
                ob_end_clean();
                print "<pre>" . $exc->getMessage() . "\n\n" . $exc->getTraceAsString() . "</pre>";
            }
    }

    if (isset($_REQUEST['debug'])){ 
        $stoptheclock = microtime(TRUE);
        $time = round($stoptheclock - $starttheclock,3);
        if (function_exists('memory_get_peak_usage')){
            $memory = memory_get_peak_usage() / 1024 / 1024 ; 
        } else {
            $memory = memory_get_usage() / 1024 / 1024 ;
        }
        print '<pre>Render time: ' . $time . ' seconds</pre>';
        print '<pre>Memory Usage: ' . $memory . ' Mb</pre>';
        print '<pre>' . print_r($e,TRUE) . '</pre>'; 

    }
}



class e {
    
    var $q;
    var $p;
    
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
    
    function qp(){
        return $this->q . '/' . $this->p;
    }
    
    function parray($item = -1){
	if ($item == -1){
	        return explode('/',$this->p);
	} else {
		$return = explode('/',$this->p);
		return @$return[$item];
	}
    }
    
    private function _find($inDirectory){
        $found = FALSE;
        
        if (isset($_REQUEST['q1'])){
            $this->q = $_REQUEST['q1'];
            $this->p = '';
        } else {
            $this->q= '';
            $this->p = '';
        }
        
        while(strlen($this->q) > 0 && !$found){
            if (file_exists($inDirectory . '/' . $this->q)){
                if ($this->_isValidDirectory($inDirectory . '/' . $this->q)){
                    if ($this->_open($inDirectory . '/' . $this->q)) {
                        $found = TRUE;
                    } else {
                        if ($this->p == ''){ $this->p = e::_popget('/', $this->q); } else {$this->p = e::_popget('/', $this->q) . '/' . $this->p;}
                        $this->q = $this->_dirup($this->q);
                    }
                }
            } else {
                if ($this->p == ''){ $this->p = e::_popget('/', $this->q); } else {$this->p = e::_popget('/', $this->q) . '/' . $this->p;}
                $this->q = $this->_dirup($this->q);
            }
        }
        
        // If we have not found anything in a subdirectory, 
        // open the "section" directory at root level (e.g. open 10.content)
        if (!$found){
            $this->_open($inDirectory);
        }
    }
    
    public function _open($directory){
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
        $title = @$directoryarray[sizeof($directoryarray) - 1];
        if ($title == ''){ $title = @$directoryarray[sizeof($directoryarray) - 2];}
        // Take the date off the front of the directory, if it is there
        if (strstr($title,'.')){
            $title = explode('.',$title);    
            $prefix = array_shift($title);
            if(!is_numeric($prefix)){ array_unshift($title,$prefix); }
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
                    
                    case 'txt': case 'text':
                        $this->$directoryarray[0]->$filearray[1] = file_get_contents($directory . '/' . $file);
                        if (!isset($this->$directoryarray[0]->html)){ $this->$directoryarray[0]->html = $this->$directoryarray[0]->$filearray[1]; }
                        break;
                        
                    case 'markdown': case 'md':
                    case 'htm': case 'html':
                        $original = '_' . $filearray[1];
                        // Load up a recognised text file and mark it down
                        $this->$directoryarray[0]->$filearray[1] = file_get_contents($directory . '/' . $file);
                        $this->$directoryarray[0]->$original  = file_get_contents($directory . '/' . $file);
                        $this->$directoryarray[0]->_html = file_get_contents($directory . '/' . $file);
                        
						$MultiMarkdown = '';
						$this->$directoryarray[0]->$filearray[1] = explode("\n",$this->$directoryarray[0]->$filearray[1]);
						foreach($this->$directoryarray[0]->$filearray[1] as $line){
							if (substr($line,0,1) == '@') {
								$line = explode(':',$line);
								$key = array_shift($line);
								$key = substr($key,1);
								$key = strtolower($key);
								$value = implode(':',$line);
								$value = trim($value);
								$this->$directoryarray[0]->$key = $value;
			
							} else {
								$MultiMarkdown .= $line . "\n";
							}
						}

                        $this->$directoryarray[0]->$filearray[1] = Markdown($MultiMarkdown);
                        if (!isset($this->$directoryarray[0]->html)){ $this->$directoryarray[0]->html = $this->$directoryarray[0]->$filearray[1]; }
                        break;
                        
                    default:
                        // Check to see if the file is binary or text
                        if ($this->_isBinaryFile($directory . '/' . $file)){
                            if(!isset($this->$directoryarray[0]->_files)){ $this->$directoryarray[0]->_files = array(); }
                            // $this->$directoryarray[0]->$filearray[1] = $directory . '/' . $file;
                            $this->$directoryarray[0]->_files[$file] = $directory . '/' . $file;
                        } else {
                            // Load the ascii content
                            $asciicontent = file_get_contents($directory . '/' . $file);
                            // Check for XML
                            libxml_use_internal_errors(true);                   // Tell XML to keep its errors to itself
                            $xmlcontent = simplexml_load_string($asciicontent); // Try to create an XML object from the string.
                            libxml_clear_errors();                              // Throw away any XML errors.
                            if (!($xmlcontent) === FALSE){
                                // This is XML content, turned into an XML object
                                $this->$directoryarray[0]->$filearray[1] = $xmlcontent;
                            } else {
                                // Add the content, no
                                $this->$directoryarray[0]->$filearray[1] = $asciicontent;
                            }
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
        // Merge in passed parameters
        $parameters = e::_searchparameters($parameters);
        
        // Set up the return array
        $return = array();
        
        $results = scandir($path);
        foreach($results as $result){
            if(isset($parameters['search_substring'])){
                $validpath = strstr($result,$parameters['search_substring']);
            } else {
                $validpath = TRUE;
            }
            if ($validpath AND e::_isValidDirectory($path . '/' . $result,TRUE)){
                $returnItem = new e();
                if ($returnItem->_open($path . '/' . $result)){
                    $return[$result] = $returnItem;
                }
            }
        }
        if (sizeof($return) == 0){
            // Try opening the item instead
            $returnItem = new e();  
            $returnItem->_open($path);
            $return[$result] = $returnItem;
        }
        
        // Sort results
        ksort($return);
        if ($parameters['sort'] !== 'desc'){ $return = array_reverse($return); }
        
        $return = array_reverse($return, TRUE);
        return $return;
    }
    
    static function _searchparameters($parameters){
        $return = array();
        $return['sort'] = 'desc';
        foreach($parameters as $parameter => $parametervalue){
            $return[$parameter] = $parametervalue;
        }
        return $return;
    }
    
    static function _searchindex($path, $parameters = array()){
        $return = array();
        $results = scandir($path);
        foreach($results as $result){
            if (e::_fileextension($result) == 'idx') {
                $idxfile = fopen($path . '/' . $result,'r');
                while ($idxpath = fgetcsv($idxfile)) {
                    $openpath = e::_shiftget('/', $path);
                    $returnItem = new e();
                    if($returnItem->_open($openpath . '/' . $idxpath[0])){
                        $return[$idxpath[0]] = $returnItem;
                    }
                }
            }
        }
        // ksort($return);
        // $return = array_reverse($return,TRUE);
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
            if ($directory == 'wp-admin') { $return = FALSE; }
            if ($directory == 'wp-content') { $return = FALSE; }
            if ($directory == 'wp-includes') { $return = FALSE; }
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
        if (e::_fileextension($file) == 'idx') { $return = FALSE; }
        if (strstr(e::_fileextension($file),'~')) { $return = FALSE; }
        return $return;
    }
    
    static function _isBinaryFile($file){
        $binary_files = array('png','jpg','jpeg','gif','pdf');
        $filetype = e::_fileextension($file);
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
        if(isset($_SERVER['SERVER_NAME'])){ 
            return $_SERVER['SERVER_NAME'];
        } else {
            return FALSE;
        }
    }
    
    static function _domainisTest(){
        $domain = e::_domain();
        if ($domain === FALSE){
            return TRUE;
        } else {
            $return = FALSE;
            if (strstr($domain,'localhost')){ $return = TRUE; }
            return $return;    
        }
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
        
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') {
        	$http = 'https://';
        } else {
        	$http = 'http://';
        }
        
        return $http . e::_domain() . $indexphp;
    }
    
    static function _fileextension($filename){
        $filename = explode('.',$filename);
        $fileextension = array_pop($filename);
        $fileextension = strtolower($fileextension);
        return $fileextension;
    }
    
    static function _popget($separator,$string){
        $stringarray = explode($separator,$string);
        $return = array_pop($stringarray);
        return $return;
    }
    
    static function _shiftget($separator,$string){
        $stringarray = explode($separator,$string);
        $return = array_shift($stringarray);
        return $return;
    }
    
    static function _goto($gotoURL, $gotoCode = 301){
        // Perform a subdirectory sage goto
        $lowergotoURL = strtolower($gotoURL);
        if (strpos($lowergotoURL,'http') === 0){
            // Fully qualified URL - we can just go there
        } else {
            $gotoURL = e::_basehref() . $gotoURL;
        }
        
        // Clean out the buffer, send a header, and die
        ob_end_clean;
        header('Location: ' . $gotoURL,$gotoCode);
        die();
    }
    
    public function _loadplugin($plugin){
        // TODO: If we are running e in a subdirectory, we need to somehow tell it
        // so that it can still find plugins.
        require_once("_e/plugins/$plugin/$plugin.php");
        $pluginvar = '_' . $plugin;
        $pluginclass = '_' . $plugin;
        if (!isset($this->$pluginvar)){
            $this->$pluginvar = new $pluginclass($this);
        }
    }
    
    public static function _textToPath($text){
        // Convert some text to a safe path
        $allowed = 'abcdefghijklmnopqrstuvwxyz1234567890.-_';
        
        $text = str_ireplace(' ', '-', $text);
        $text = strtolower($text);
        
        $text = str_split($text);
        $return = '';
        
        foreach($text as $key=>$char){
            if(strstr($allowed,$char)){
                $return .= $char;
            }
        }
        
        return $return;
    }
    
    public static function _pathToText($path){
        $path = str_ireplace('-', ' ', $path);
        $path = ucwords($path);
        return $path;
    }
    
    static public function trace($message){
        print $message . "\n";
    }
    
    static public function _trace($message){
    	e::trace($message);
    }
    
    static public function dump($data,$debuffer = TRUE,$staylive = FALSE){
        if($debuffer) { ob_end_clean(); }
        print "<pre>" . print_r($data,TRUE) . "</pre>";
        // print "<pre>" . print_r(debug_backtrace(),TRUE) . "</pre>";
        if(!$staylive){ die(); }
    }
    
    static public function _dump($data){
    	e::dump($data);
    }
    
    static public function debug($on = TRUE, $level = -1){
    	if ($on){
    		error_reporting($level);
    		ini_set('display_errors', 1);
    	} else {
    		ini_set('display_errors', 0);
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


class eSiteMap {
	
	private $map;
	
	public function xml(){
		// Get the master set of directories.
		$directories = scandir('.');
		$this->map = array();
		// Work your way through the directories, mapping out the content
		foreach($directories as $directory){
			// Work through directories, processing each in order.
			if (e::_isValidDirectory($directory)){
				$this->mapSubDirectory($directory);
			}
		}
		
		$return = '<?xml version="1.0" encoding="utf-8"?>
					<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
		foreach($this->map as $mappoint){
			$mappoint = str_ireplace('&', '&amp;', $mappoint);
			$return .= "
						<url>
							<loc>" . e::_basehref() . $mappoint . "</loc>
      						<lastmod>" . date('c',time()) . "</lastmod>
      						<changefreq>daily</changefreq>
      						<priority>0.8</priority>
   					</url>";
		}
		$return .= '</urlset>';
		return $return;
	} 
	
	private function mapSubDirectory($directory){
		$directories = scandir($directory);
		foreach($directories as $subdirectory){
			if(e::_isValidDirectory($directory . '/' . $subdirectory)){
				
				$mapdirectory = explode('/',$directory);
				unset($mapdirectory[0]);
				$mapdirectory[] = $subdirectory;
				$mapdirectory = implode('/',$mapdirectory);
								
				$this->map[$mapdirectory] = $mapdirectory;
				
				$this->mapSubDirectory($directory . '/' . $subdirectory);
			}
		}
	}  
	
}
?>
