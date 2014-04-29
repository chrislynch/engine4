<?php 
if(isset($_POST['_OP'])){
	// Save the id
	$tabledata = array();
	$tabledata['_timestamp'] = 'UNIX_TIMESTAMP()';
	
	foreach($_POST as $field => $value){
		if(substr($field,0,1) == '_'){
			$tabledata[$field] = $value;
		}
	}
	if($_POST['_ID'] == 0){
		$_POST['_ID'] = $this->_db->insertinto('id',$tabledata,'_ID');	
		print "post = {$_POST['_ID']}<br>";
	} else {
		$this->_db->replaceinto('id',$tabledata,'_ID');
	}

	// Save the data
	foreach($_POST as $field => $value){
		$tabledata = array();
		$tabledata['_ID'] = $_POST['_ID'];
		$tabledata['Field'] = $field;
		$tabledata['Data'] = $value;
		$this->_db->replaceinto('data',$tabledata,'_ID');
	}

	// Reload for sanity and safety
	$this->_goto("admin/data/edit?ID={$_POST['_ID']}");
}

?>

<form action="" method="POST">
<?php

// TODO: Add some code to handle new items
if(@$_GET['ID'] > 0){
	// Editing an existing item
	$datarecord = $this->_cms->loadfromdata($_GET['ID']);
	if(isset($datarecord['_type'])){ $fields = $this->_cms->loadfields($datarecord['_type']); } else { $fields = $this->_cms->loadfields($datarecord['_type']); }
} else {
	// Creating a new item
	$datarecord = array();
	$datarecord['_ID'] = 0;
	$datarecord['_type'] = @$_GET['type'];
	$fields = $this->_cms->loadfields($datarecord['_type']);
}

// Output the _ID to help us save
print "<input type='hidden' name='_ID' value='{$_GET['ID']}'>";
unset($datarecord['_ID']);

foreach($fields as $field){
	print "<h2>" . $field['title'] . "</h2>";
	if(!isset($datarecord[$field['field']])){ $datarecord[$field['field']] = $field['default']; }
	switch($field['input-type']){
		case 'text':
			$datarecord[$field['field']] = str_ireplace("'","\'",$datarecord[$field['field']]);
			print "<input type='text' name='{$field['field']}' size='{$field['cols']}' value='{$datarecord[$field['field']]}'>";
			break;
		case 'textarea':
			print "<textarea name='{$field['field']}' rows='{$field['rows']}' cols='{$field['cols']}'>{$datarecord[$field['field']]}</textarea>";
	}
	unset($datarecord[$field['field']]);
}

print "<p><input type='submit' name='_OP' value='Save Data'></p>";

print "<hr>";

foreach($datarecord as $field => $value){
	$datarecord[$field] = str_ireplace("'","\'",$value);
	if(substr($field,0,1) !== '_'){
		print "<h3>$field</h3>";
		print "<pre>" . $value . "</pre>";
	}
	print "<input type='hidden' name='$field' value='$value'>";
}

?>
</form>