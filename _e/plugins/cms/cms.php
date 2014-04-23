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
	
}