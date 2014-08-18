<table>
<tr>
	<th>ID</th>
	<th>Type</th>
	<th>Name</th>
	<th>Status</th>
	<th>URI</th>
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

$indexrecords = $this->_db->query("SELECT * FROM things ORDER BY ID DESC LIMIT $start,$end");

while($indexrecord = $indexrecords->fetch()){
	// Make the status pretty
	switch($indexrecord['Status']){
		case 1: $indexrecord['_status'] = 'Published'; break;
		default: $indexrecord['_status'] = 'Unpublished'; break;
	}

	print "<tr>
		<td>{$indexrecord['ID']}</td>
		<td>{$indexrecord['Type']}</td>
		<td>{$indexrecord['Name']}</td>
		<td>{$indexrecord['Status']}</td>
		<td><a href='{$indexrecord['URI']}' target='_blank'>{$indexrecord['URI']}</a></td>
		<td>{$indexrecord['Timestamp']}</td>
		<td><a href='admin/data/edit?ID={$indexrecord['ID']}'>Edit</a></td>
	</tr>";
}
?>

</table>


<?php
/* 
// Output a pager
$indexcount = $this->_db->result("SELECT COUNT(0) FROM id");
$pages = ($indexcount / 30) + 1;
print "<p>Records = $indexcount / Pages = $pages</p>";
for ($i = 1; $i <= $pages; $i++) {
	print "<a href='admin/data?page=$i'>$i</a>&nbsp;&nbsp;";
}
*/
?>
