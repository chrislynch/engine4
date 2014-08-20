<?php 
$corefields = array('ID','Name','URI','Type','Status');
$coregroups = array('_core','_control','_content');

if(isset($_POST['_OP'])){
	// Set defaults
	if(!(isset($_POST['Status']))) { $_POST['Status'] = 0; }

	// Start a spare thing to collect data
	$thing = array();
		
	// Build the tabledata array
	$tabledata = array();
	foreach($_POST as $field => $value){
		if(in_array($field,$corefields)){
			$tabledata[$field] = $value;
		}
	}
	$thing = $tabledata;

	// Save to things table
	if($_POST['ID'] == 0){
		unset($tabledata['ID']);
		$_POST['ID'] = $this->_db->insertinto('things',$tabledata);	
	} else {
		$this->_db->replaceinto('things',$tabledata);
	}

	// Save to things_data table
	$this->_db->update("DELETE FROM things_data WHERE ID = " . $_POST['ID']);
	foreach($_POST as $field => $value){
		if(substr($field,0,1) !== '_'){
			$tabledata = array();
			$tabledata['ID'] = $_POST['ID'];
			$tabledata['Field'] = $field;
			$tabledata['Value'] = $value;
			$this->_db->replaceinto('things_data',$tabledata);
			$thing[$field] = $value;
		}
	}

	// Save any inbound files
	foreach($_FILES as $field => $file){
		if($file['error'] == 0){
			$newfilename = "_custom/_default/content/uploads/{$file['name']}";
			move_uploaded_file($file['tmp_name'], $newfilename);
			$tabledata = array();
			$tabledata['ID'] = $_POST['ID'];
			$tabledata['Field'] = $field;
			$tabledata['Value'] = $newfilename;
			$this->_db->replaceinto('things_data',$tabledata);
			$thing[$field] = $newfilename;
		} else {

		}
	}

	// Save any default or cached values (like old files)
	foreach($_POST as $field => $value){
		if(substr($field,0,1) == '_'){
			$field = substr($field,1);
			if(!(isset($thing[$field]))){
				$tabledata = array();
				$tabledata['ID'] = $_POST['ID'];
				$tabledata['Field'] = $field;
				$tabledata['Value'] = $value;
				$this->_db->replaceinto('things_data',$tabledata);
				$thing[$field] = $value;
			}
		}
	}

	// Reload for sanity and safety
	$this->_goto("admin/data/edit?ID={$_POST['ID']}");

} else {
	if($_GET['ID'] <> 0){
		$thing = _cms::loadID($_GET['ID']);	
	} else {
		$thing = array();
		if(isset($_GET['type'])){
			$thing['Type'] = $_GET['type'];
		} else {
			$thing['Type'] = 'post';
		}
	}
}

?>

<form action="" method="POST" enctype="multipart/form-data">
	<input type="hidden" name="ID" value="<?= isset($_GET['ID']) ? $_GET['ID'] : 0; ?>">
	<input type="hidden" name="Type" value="<?= $thing['Type'] ?>">
	<div class='row'><div class="col-xs-12 col-sm-12 col-md-12 col-lg-12"><h1>Editing <?= @$thing['Name'] == '' ? 'New Item' : $thing['Name'] ?></h1></div>
	</div>

	<div class='row'>
		<div class="col-xs-12 col-sm-8 col-md-8 col-lg-8">
			<?php
				$fields = _cms::loadfields($thing['Type']);
				foreach($fields as $field){ 
					if($field['FieldGroup'] == '_core'){
						print outputField($field,$thing); 
					}		
				}
			?>			
		</div>
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 shade">
			<h2>Control Panel</h2>
			<?php
				$fields = _cms::loadfields($thing['Type']);
				foreach($fields as $field){ 
					if($field['FieldGroup'] == '_control'){
						print outputField($field,$thing); 
					}		
				}
			?>			
			<br><input type="submit" name="_OP" value="Save Content" class="btn btn-primary"><br><br>
		</div>
	</div>
	<div class='row'>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php
				$fields = _cms::loadfields($thing['Type']);
				foreach($fields as $field){ 
					if($field['FieldGroup'] == '_content'){
						print outputField($field,$thing); 
					}		
				}
			?>
		</div>

	</div>
	<div class='row'>
		<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12">
			<?php
				$group = "";
				$fields = _cms::loadfields($thing['Type']);
				foreach($fields as $field){ 
					if(!(in_array($field['FieldGroup'],$coregroups))){
						if($field['FieldGroup'] !== $group){
							print "<h2>{$field['FieldGroup']}</h2>";
							$group = $field['FieldGroup'];
						}
						print outputField($field,$thing);
					}		
				}
			?>
		</div>

	</div>
</form>


<?php

function outputField($field,$thing){
	$value = @$thing[$field['FieldName']];
	
	switch ($field['FieldType']){
		case 'text':
			$return = "<h3>{$field['FieldLabel']}</h3>";
			$return .= "<input type='text' name='{$field['FieldName']}' value='{$value}'>";
			break;
		case 'markdown':
		case 'textarea':	
			$value = htmlspecialchars($value);
			$return = "<h3>{$field['FieldLabel']}</h3>";
			$return .= "<textarea name='{$field['FieldName']}' rows='{$field['FieldSize']}'>{$value}</textarea>";
			break;
		case 'image':
		case 'file':
			$return = "<h3>{$field['FieldLabel']}</h3>";
			$return .= "<input type='hidden' name='_{$field['FieldName']}' value='$value'>";
			$return .= "<a href='{$value}' target='_blank'>{$value}</a><input name='{$field['FieldName']}' type='file'>";
			break;
		case 'checkbox':
			if($value) { $value = " checked='checked'"; } else { $value = ""; }
			$return = "<input type='checkbox' name='{$field['FieldName']}' value='1'{$value}>{$field['FieldLabel']}<br>";
			break;
		default:
		// We don't know what to do with this field, so we output it as a hidden and a label
		$return = "<input type='hidden' name='{$field['FieldName']}' value='{$value}'><p>{$value}</p>";
	}
	$return .= "<p><small>{$field['Help']}</small></p>";
	return $return;
}

?>

