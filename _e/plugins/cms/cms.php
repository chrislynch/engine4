<?php
class _cms {

	private $e;

	public function __construct(&$e){
		$this->e =& $e;
	}

	public function loadfromdata($ID){
		$indexrecorddata = $this->e->_db->select("SELECT * FROM data WHERE ID = $ID");
		$indexrecordarray = array();
		while($indexrecorddatarecord = mysql_fetch_assoc($indexrecorddata)){
			$indexrecordarray[$indexrecorddatarecord['Field']] = $indexrecorddatarecord['Data'];
		}
		return $indexrecordarray;
	}
	
	public function loadfields($type){
		// Load up the field definitions for a given type 
		$this->e->loadPlugin('csv');
		$templatefields = $this->e->_csv->loadCSV('_data/templates.csv');
		$template = array();
		foreach($templatefields as $templatefield){
			if($templatefield['type'] == $type){
				$template[] = $templatefield;
			}
		}
	}
	
}