<?php
if(!isset($_GET['page'])){ $_GET['page'] = 1;}
$page = $_GET['page'];
$start = ($page -1) * 30;
$end = $start + 30;

$indexrecords = $this->_db->query("SELECT * FROM id ORDER BY id DESC LIMIT $start,$end");

while($indexrecord = mysql_fetch_assoc($indexrecords)){
	$indexrecorddata = $this->_db->select("SELECT * FROM data WHERE ID = {$indexrecord['ID']}");
	$indexrecordarray = array();
	while($indexrecorddatarecord = mysql_fetch_assoc($indexrecorddata)){
		$indexrecordarray[$indexrecorddatarecord['Field']] = $indexrecorddatarecord['Data'];
	}
	print "<h2><a href='admin/data?ID={$indexrecord['ID']}'>" . $indexrecordarray['post_title'] . "</a></h2>";
}
?>