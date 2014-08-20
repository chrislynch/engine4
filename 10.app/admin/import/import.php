<?php
$imported = array();
$postdirs = scandir("10.content/posts");
foreach ($postdirs as $postdir) {
	foreach($postdirs as $postdir){
		if($postdir !== '.' && $postdir !== '..'){
			$postfiles = glob("10.content/posts/$postdir/*.htm*");
			foreach($postfiles as $postfile){
				if(in_array($postfile,$imported)) { break; }
				print "Loading $postfile<br>\n\l";
				$imported[] = $postfile;
				$thing = array();			
				$thingdata = array();

				$thing['URI'] = $postdir;
				$thing['Status'] = 1;
				$thing['Type'] = 'post';

				// Take the date off the front of the directory, if it is there
				$title = $postfile;
				$title = explode('/',$title);
				$title = array_pop($title);
				$title = explode('-',$title);
				$timestamp = array_shift($title);
				$title = implode(' ',$title);
				$title = str_ireplace('.html','',$title);

				$thing['Timestamp'] = substr($timestamp,0,4) . '-' . substr($timestamp,3,2) . '-' . substr($timestamp,5,2) . ' 00:00:00';

				if (strstr($title,'.')){
				    $title = explode('.',$title);    
				    $prefix = array_shift($title);
				    if(!is_numeric($prefix)){ array_unshift($title,$prefix); }
				    $title = implode('.',$title);
				}
				$title = str_ireplace('-', ' ', $title);
				$title = ucwords($title);
				$thing['Name'] = $title;

				$ID = $this->_db->replaceinto('things',$thing);
				$thing['ID'] = $ID;

				// Load the content and strip out any variables
				$fileContent = file_get_contents("$postfile");
				$fileContent = explode("\n",$fileContent);
				$HTML = '';

				foreach($fileContent as $line){
					if (substr($line,0,1) == '@') {
						$line = explode(':',$line);
						$key = array_shift($line);
						$key = substr($key,1);
						$key = strtolower($key);
						$value = implode(':',$line);
						$value = trim($value);
					
						if($key == 'title'){
							$thing['Name'] = $value;
							$ID = $this->_db->replaceinto('things',$thing);
						} else {
							$thingdata = array();
							$thingdata['ID'] = $ID;
							$thingdata['Field'] = $key;
							$thingdata['Value'] = $value;
							$this->_db->replaceinto('things_data',$thingdata);					
						}

							
					} else {
						$HTML .= $line . "\n";
					}
				}
				$thingdata = array();
				$thingdata['ID'] = $ID;
				$thingdata['Field'] = 'HTML';
				$thingdata['Value'] = $HTML;
				$this->_db->replaceinto('things_data',$thingdata);						
				break; // Skip out of this directory/result set and move on
			}
		}
		
	}
}
die();
?>
