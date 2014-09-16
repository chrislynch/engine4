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

}
