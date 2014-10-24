<?php 
$corefields = array('ID','Name','URI','Type','Status','Timestamp','System');
$coregroups = array('_core','_control','_content');

if(isset($_POST['_OP'])){
	// Save the thing.
	$savedThing = _cms::saveThing($_POST,$_FILES);
	$_POST['ID'] = $savedThing['ID'];
	// Reload for thing for sanity and safety
	$this->_goto("admin/data/edit?ID={$_POST['ID']}");

} else {
	if(isset($_GET['ID']) && strval(@$_GET['ID']) !== 0){
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

<form action="admin/data/edit?debug" method="POST" enctype="multipart/form-data">
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
		<div class="col-xs-12 col-sm-4 col-md-4 col-lg-4 background-secondary-1-0">
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
	if(strlen(@$field['FieldDefaultValue']) > 0 && !(isset($thing[$field['FieldName']]))){
		$value = $field['FieldDefaultValue'];
	}

	switch ($field['FieldType']){
		case 'text':
		case 'url':
		case 'tags':
		case 'youtube':
			$value = htmlspecialchars($value);
			$return = "<h3>{$field['FieldLabel']}</h3>";
			$return .= "<input type='text' name='{$field['FieldName']}' value=\"$value\">";
			break;
		case 'hidden':
			$return = "<input type='hidden' name='{$field['FieldName']}' value=\"$value\">";
			break;
		case 'password':
			$value = htmlspecialchars($value);
			$return = "<h3>{$field['FieldLabel']}</h3>";
			$return .= "<input type='password' name='{$field['FieldName']}' value=\"$value\">";
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

