<?php
class _cms {

	private $e;

	public function __construct(&$e){
		$this->e =& $e;
	}

	static public function loadThing(&$thing) {
		global $e;
		// Load all of the data associated with a thing.
		$postdatafields = $e->_db->query("SELECT * FROM things_data WHERE ID = ${thing['ID']}");
		while($postdata = $postdatafields->fetch()){
			// if(!isset($thing[$postdata['Field']])){
				$thing[$postdata['Field']] = $postdata['Value'];
			// }
		}
		return $thing;
	}

	static public function loadID($ID){
		global $e;
		$posts = $e->_db->query('SELECT * FROM things WHERE ID = ' . $ID);
		$post = $posts->fetch();
		_cms::loadThing($post);
		return $post;
	}
	
	static public function loadtypes(){
		global $e;
		$e->_loadplugin('csv');
		$templatefields = $e->_csv->loadCSV('_custom/_default/config/types.csv');
		$types = array();
		foreach($templatefields as $type){
			if($type['Type'] == ''){
				$types['Default'] = 'Default';
			} else {
				$types[$type['Type']] = $type['Type'];
			}
		}
		return $types;
	}
	
	static public function loadfields($type){
		// Load up the field definitions for a given type 
		global $e;
		$e->_loadplugin('csv');
		$templatefields = $e->_csv->loadCSV('_custom/_default/config/types.csv');
		$template = array();
		foreach($templatefields as $templatefield){
			if($templatefield['Type'] == $type){
				$template[] = $templatefield;
			} 
		}
		return $template;
	}
	
	static public function renderThing(&$thing){
		// Apply a set of rendering functions to a thing to render it properly.
		// So, for example, apply Markdown rendering to a thing 
		$fields = _cms::loadfields($thing['Type']);
		$thing['_meta'] = array();
		$thing['_meta']['tags'] = array();
		foreach($fields as $field){
			$fieldName = $field['FieldName'];
			$fieldType = $field['FieldType'];
			switch ($fieldType){
				case 'markdown':
					$thing[$fieldName] = Markdown($thing[$fieldName]);
					break;
				case 'tags':
					$tags = explode(',',$thing[$fieldName]);
					$ltags = array();
					foreach($tags as $tag){
						$tag = trim($tag);
						$thing['_meta']['tags'][] = $tag;
						if(strlen($tag) > 0) {
							$ltags[] = "<a href='$fieldName/$tag'>$tag</a>";	
						}
					}
					$thing[$fieldName] = $ltags = implode(', ',$ltags);
					break;
				case 'youtube':
					$youtube = explode('?v=',$thing[$fieldName]);
					$youtube = $youtube[1];
					$thing[$fieldName] = '<iframe width="480" height="360" src="//www.youtube.com/embed/' . $youtube . '?rel=0" frameborder="0" allowfullscreen></iframe>';
					break;
				default:
					// Do nothing
			}
		}
	}

	static public function prepareThing(&$thing){
		// Prepares a thing to be saved.
		$fields = _cms::loadfields($thing['Type']);
		foreach($fields as $field){
			$fieldName = $field['FieldName'];
			$fieldType = $field['FieldType'];
			switch ($fieldType){
				case 'url':
					$thing[$fieldName] = urlencode(strtolower($thing[$fieldName]));
					break;
				default:
					// Do nothing
			}
		}
	}

	static public function corefields(){
		$corefields = array('ID','Name','URI','Type','Status','Timestamp','System');		
		return $corefields;
	}

	static public function newThing(){
		$corefields = _cms::corefields();
		$thing = array();
		foreach($corefields as $field){
			$thing[$field] = '';
		}
	}

	static public function saveThing($post,$files = array()){
		global $e;

		$corefields = _cms::corefields();
		// Set defaults
		if(!(isset($post['Status']))) { $post['Status'] = 0; }

		// Start a spare thing to collect data
		$thing = array();
			
		// Build the tabledata array
		$tabledata = array();
		foreach($post as $field => $value){
			if(in_array($field,$corefields)){
				$tabledata[$field] = $value;
			}
		}
		$thing = $tabledata;

		// Validate URL
		if($tabledata['URI'] == ''){ $tabledata['URI'] = e::_textToPath($tabledata['Name']); print "Calculated URI '{$tabledata['URI']}'"; }
		$tabledata['URI'] = strtolower(urlencode($tabledata['URI'])); 
		// Check that this URL doesn't already exist
		$URICount = $e->_db->result("SELECT COUNT(0) FROM things WHERE URI = '{$tabledata['URI']}' AND ID <> {$tabledata['ID']}");
		if ($URICount > 0) { $tabledata['URI'] .= '-' . $URICount; }

		// Validate Timestamp - let automatic content take over if nothing is set.
		if($tabledata['Timestamp'] == ''){ unset($tabledata['Timestamp']); }

		// Save to things table
		if($post['ID'] == 0){
			unset($tabledata['ID']);
			$post['ID'] = $e->_db->insertinto('things',$tabledata);
			$thing['ID'] = $post['ID'];	
		} else {
			$e->_db->replaceinto('things',$tabledata);
		}

		// Save to things_data table
		$e->_db->update("DELETE FROM things_data WHERE ID = " . $post['ID']);
		foreach($post as $field => $value){
			if(!(in_array($field,$corefields))){
				$tabledata = array();
				$tabledata['ID'] = $post['ID'];
				$tabledata['Field'] = $field;
				$value = _cms::convert_ascii($value); // Get rid of non-ASCII characters
				$tabledata['Value'] = $value;
				$check = $e->_db->replaceinto('things_data',$tabledata);
				if ($check == 0) { print "Error saving field $field<br>"; }
				$thing[$field] = $value;
			}
		}

		// Save any inbound files
		foreach($files as $field => $file){
			if($file['error'] == 0){
				$newfilename = "_custom/_default/content/uploads/{$file['name']}";
				move_uploaded_file($file['tmp_name'], $newfilename);
				$tabledata = array();
				$tabledata['ID'] = $post['ID'];
				$tabledata['Field'] = $field;
				$tabledata['Value'] = $newfilename;
				$e->_db->replaceinto('things_data',$tabledata);
				$thing[$field] = $newfilename;
			} else {

			}
		}

		// Save any default or cached values (like old files)
		foreach($post as $field => $value){
			if(substr($field,0,1) == '_'){
				$field = substr($field,1);
				if(!(isset($thing[$field]))){
					$tabledata = array();
					$tabledata['ID'] = $post['ID'];
					$tabledata['Field'] = $field;
					$tabledata['Value'] = $value;
					$e->_db->replaceinto('things_data',$tabledata);
					$thing[$field] = $value;
				}
			}
		}

		return $thing;
	}

	static public function convert_ascii($string) { 
  		// Replace Single Curly Quotes
		$search[]  = chr(226).chr(128).chr(152);
		$replace[] = "'";
		$search[]  = chr(226).chr(128).chr(153);
		$replace[] = "'";

		// Replace Smart Double Curly Quotes
		$search[]  = chr(226).chr(128).chr(156);
		$replace[] = '"';
		$search[]  = chr(226).chr(128).chr(157);
		$replace[] = '"';

		// Replace En Dash
		$search[]  = chr(226).chr(128).chr(147);
		$replace[] = '--';

		// Replace Em Dash
		$search[]  = chr(226).chr(128).chr(148);
		$replace[] = '---';

		// Replace Bullet
		$search[]  = chr(226).chr(128).chr(162);
		$replace[] = '*';

		// Replace Middle Dot
		$search[]  = chr(194).chr(183);
		$replace[] = '*';

		// Replace Ellipsis with three consecutive dots
		$search[]  = chr(226).chr(128).chr(166);
		$replace[] = '...';

		// Apply Replacements
		$string = str_replace($search, $replace, $string);

		// Remove any non-ASCII Characters
		$string = preg_replace("/[^\x01-\x7F]/","", $string);

		return $string; 
	}

}
