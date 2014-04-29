<?php
class _cms {

	private $e;

	public function __construct(&$e){
		$this->e =& $e;
	}

	public function loadfromdata($ID){
		$indexrecorddata = $this->e->_db->select("SELECT * FROM data WHERE _ID = $ID");
		$indexrecordarray = array();
		while($indexrecorddatarecord = mysql_fetch_assoc($indexrecorddata)){
			$indexrecordarray[$indexrecorddatarecord['Field']] = $indexrecorddatarecord['Data'];
		}
		return $indexrecordarray;
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