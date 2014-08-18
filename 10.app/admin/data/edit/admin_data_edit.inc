<?php 
if(isset($_POST['_OP'])){
	// Build the tabledata array
	$tabledata = array();
	
	foreach($_POST as $field => $value){
		if(substr($field,0,1) == '_' && $field !== '_OP'){
			$tabledata[substr($field,1)] = $value;
		}
	}

	// Save to things table
	if($_POST['_ID'] == 0){
		unset($tabledata['ID']);
		$_POST['_ID'] = $this->_db->insertinto('things',$tabledata);	
		print "post = {$_POST['_ID']}<br>";
	} else {
		$this->_db->replaceinto('things',$tabledata);
	}

	// Save to things_data table
	foreach($_POST as $field => $value){
		$tabledata = array();
		$tabledata['ID'] = $_POST['_ID'];
		$tabledata['Field'] = $field;
		$tabledata['Value'] = $value;
		$this->_db->replaceinto('things_data',$tabledata);
	}

	// Reload for sanity and safety
	$this->_goto("admin/data/edit?ID={$_POST['_ID']}");
} else {
	if($_GET['ID'] > 0){
		$thing = _cms::loadID($_GET['ID']);	
	} else {
		$thing = array();
	}

}

?>

<form action="" method="POST">
	<div class='row'>
	<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
	<h1>Editing "<?= @$thing['Name'] == '' ? 'New Item' : $thing['Name'] ?>" (ID: <?= @$thing['ID'] == '' ? 0 : $thing['ID'] ?>)</h1>
	</div>
	</div>
	<div class='row'>
		<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
			<input type="hidden" name="_ID" value="<?= isset($_GET['ID']) ? $_GET['ID'] : 0; ?>">
			<h2>Name</h2>
			<input type="text" size="30" name="_Name" value="<?= @$thing['Name'] ?>"><br>
			<small>The name field does not need to be unique, but should clearly distinguish what this item is.<br>The name field will be used in webpage titles and to calculate the URI if it is left blank.</small>
			<h2>URI</h2>
			<input type="text" size="30" name="_URI" value="<?= @$thing['URI'] ?>">
			<small>The URI "slug" is the path to this item when it is being viewed online.<br>Leave this field blank on new items to have the URI calculated automatically.</small>
		</div>
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 shade">
			<h2>Control Panel</h2>
			<input type="checkbox" name="_Status" value="1" <?= @$thing['Status'] == 1 ? 'checked="checked"' : ''?>>&nbsp;Publish this content<br><br>
			<input type='submit' name='_OP' value='Save Content' class="btn btn-primary"><br><br>
		</div>
	</div>
	<div class='row'>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h2>Content</h2>
			<textarea name="HTML" rows="15" style="width: 100%" ><?= htmlspecialchars(@$thing['HTML']) ?></textarea>
		</div>
	</div>
	<div class='row'>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<h2>Fields</h2>
			<?php
				$fields = _cms::loadfields('post');
				foreach($fields as $field){ print outputField($field,$thing); }
			?>
		</div>
	</div>
</form>


<?php

function outputField($field,$thing){
	$value = @$thing[$field['FieldName']];
	
	switch ($field['FieldType']){
		case 'text':
			$return = "<input type='text' name='{$field['FieldName']}' value='{$value}'>";
			break;
		case 'textarea':	
			$value = htmlspecialchars($value);
			$return = "<textarea name='{$field['FieldName']}'>{$value}</textarea>";
			break;
		default:
		// We don't know what to do with this field, so we output it as a hidden and a label
		$return = "<input type='hidden' name='{$field['FieldName']}' value='{$value}'><p>{$value}</p>";
	}
	$return = "<h3>{$field['FieldName']}</h3>" . $return;
	return $return;
}

?>

<?php
/*
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
*/
?>


