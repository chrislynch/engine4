<table>
<tr>
	<th>ID</th>
	<th>Type</th>
	<th>Name (TODO: Make this configurable via templates.csv)</th>
	<th>Status</th>
	<th>Security</th>
	<th>Timestamp</th>
	<th>&nbsp</th>
</tr>

<?php
// Configure paging
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
	// Make the status pretty
	switch($indexrecordarray['_status']){
		case 1: $indexrecordarray['_status'] = 'Published'; break;
		default: $indexrecordarray['_status'] = 'Unpublished'; break;
	}
	switch($indexrecordarray['_security']){
		case 1: $indexrecordarray['_status'] = 'Secured'; break;
		default: $indexrecordarray['_status'] = 'Public'; break;
	}
	print "<tr>
				<td>{$indexrecord['_ID']}</td>
				<td>{$indexrecord['_type']}</td>
				<td>{$indexrecordarray['title']}</td>
				<td>{$indexrecord['_status']}</td>
				<td>{$indexrecord['_security']}</td>
				<td>{$indexrecord['_timestamp']}</td>
				<td><a href='admin/data/edit?ID={$indexrecord['_ID']}'>Edit</a></td>
			</tr>";
}
?>

</table>

<?php 
// Output a pager
$indexcount = $this->_db->result("SELECT COUNT(0) FROM id");
$pages = ($indexcount / 30) + 1;
print "<p>Records = $indexcount / Pages = $pages</p>";
for ($i = 1; $i <= $pages; $i++) {
	print "<a href='admin/data?page=$i'>$i</a>&nbsp;&nbsp;";
}
?>