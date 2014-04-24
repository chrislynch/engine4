<?php 
$indexrecords = $this->_db->query("SELECT * FROM id ORDER BY id DESC LIMIT 20");
while($indexrecord = mysql_fetch_assoc($indexrecords)){
	$indexrecorddata = $this->_db->select("SELECT * FROM data WHERE ID = {$indexrecord['ID']}");
	$indexrecordarray = array();
	while($indexrecorddatarecord = mysql_fetch_assoc($indexrecorddata)){
		$indexrecordarray[$indexrecorddatarecord['Field']] = $indexrecorddatarecord['Data']; 
	}
	print "<h2>" . $indexrecordarray['post_title'] . "</h2>";
}
?>