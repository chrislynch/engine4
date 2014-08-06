<?php
if ($this->p == ''){
	// Homepage
	if(!isset($_GET['page'])){ $_GET['page'] = 1;}
	$page = $_GET['page'];
	$start = ($page -1) * 30;
	// $end = $start + 30;
	$end = 30;

	$indexrecords = $this->_db->query("SELECT * FROM id ORDER BY _ID DESC LIMIT $start,$end");
	
	while($indexrecord = mysql_fetch_assoc($indexrecords)){
		$indexrecorddata = $this->_db->select("SELECT * FROM data WHERE _ID = {$indexrecord['_ID']}");
		$indexrecordarray = array();
		while($indexrecorddatarecord = mysql_fetch_assoc($indexrecorddata)){
			$indexrecordarray[$indexrecorddatarecord['Field']] = $indexrecorddatarecord['Data'];
		}
		print "<h2><a href='view/{$indexrecord['_ID']}'>" . $indexrecordarray['title'] . "</a></h2>";
		print "<p>{$indexrecordarray['excerpt']}</p>";
	}
	// Output a pager
	$indexcount = $this->_db->result("SELECT COUNT(0) FROM id");
	$pages = ($indexcount / 30) + 1;
	print "<p>Records = $indexcount / Pages = $pages</p>";
	for ($i = 1; $i <= $pages; $i++) {
		print "<a href='?page=$i'>$i</a>&nbsp;&nbsp;";
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