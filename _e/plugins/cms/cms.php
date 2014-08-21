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
			$thing[$postdata['Field']] = $postdata['Value'];
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
	
	public function loadtypes(){
		$this->e->_loadplugin('csv');
		$templatefields = $this->e->_csv->loadCSV('_custom/_default/config/types.csv');
		$types = array();
		foreach($templatefields as $type => $fields){
			if($type['type'] == ''){
				$types['Default'] = 'Default';
			} else {
				$types[$type['type']] = $type['type'];
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
						$ltags[] = "<a href='$fieldName/$tag'>$tag</a>";	
					}
					$thing[$fieldName] = $ltags = implode(', ',$ltags);
				default:
					// Do nothing
			}
		}
	}

}
