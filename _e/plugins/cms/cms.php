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
		print_r($post);
		return $post;
	}
	
	public function loadtypes(){
		$this->e->_loadplugin('csv');
		$templatefields = $this->e->_csv->loadCSV('_data/templates.csv');
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
	
	public function loadfields($type){
		// Load up the field definitions for a given type 
		$this->e->_loadplugin('csv');
		$templatefields = $this->e->_csv->loadCSV('_data/templates.csv');
		$template = array();
		foreach($templatefields as $templatefield){
			if($templatefield['type'] == $type){
				$template[] = $templatefield;
			}
		}
		return $template;
	}
	
}
