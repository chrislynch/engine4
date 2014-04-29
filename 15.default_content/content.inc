<?php
if ($this->p == ''){
	// Homepage
	$indexrecords = $this->_db->query("SELECT * FROM id ORDER BY _ID DESC LIMIT 20");
	while($indexrecord = mysql_fetch_assoc($indexrecords)){
		$indexrecorddata = $this->_db->select("SELECT * FROM data WHERE _ID = {$indexrecord['_ID']}");
		$indexrecordarray = array();
		while($indexrecorddatarecord = mysql_fetch_assoc($indexrecorddata)){
			$indexrecordarray[$indexrecorddatarecord['Field']] = $indexrecorddatarecord['Data'];
		}
		print "<h2><a href='view/{$indexrecord['_ID']}'>" . $indexrecordarray['title'] . "</a></h2>";
	}
} else {
	// We are looking at a specific thing, probably.
	if(is_numeric($this->parray(1))){
		// Found an ID
		$content = $this->_cms->loadfromdata($this->parray(1));
		print "<h1>{$content['title']}</h1>";
		print Markdown($content['html']);
	} else {
		// We are trying to search for something, maybe.
		// This is where we need a full text index for swift and nifty searching.	
	}
} 

?>