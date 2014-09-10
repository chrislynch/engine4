<?php 
$corefields = array('ID','Name','URI','Type','Status','Timestamp','System');
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

	// Validate URL
	if($tabledata['URI'] == ''){ $tabledata['URI'] = e::_textToPath($tabledata['Name']); print "Calculated URI '{$tabledata['URI']}'"; }
	$tabledata['URI'] = strtolower(urlencode($tabledata['URI'])); 
	// Check that this URL doesn't already exist
	$URICount = $this->_db->result("SELECT COUNT(0) FROM things WHERE URI = '{$tabledata['URI']}' AND ID <> {$tabledata['ID']}");
	if ($URICount > 0) { $tabledata['URI'] .= '-' . $URICount; }

	// Validate Timestamp - let automatic content take over if nothing is set.
	if($tabledata['Timestamp'] == ''){ unset($tabledata['Timestamp']); }

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
		if(!(in_array($field,$corefields))){
			$tabledata = array();
			$tabledata['ID'] = $_POST['ID'];
			$tabledata['Field'] = $field;
			$value = convert_ascii($value); // Get rid of non-ASCII characters
			$tabledata['Value'] = $value;
			$check = $this->_db->replaceinto('things_data',$tabledata);
			if ($check == 0) { print "Error saving field $field<br>"; }
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
	if(strlen(@$field['FieldDefaultValue']) > 0 && !(isset($thing[$field['FieldName']]))){
		$value = $field['FieldDefaultValue'];
	}

	switch ($field['FieldType']){
		case 'text':
		case 'url':
		case 'tags':
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

function convert_ascii($string) 
{ 
  // Replace Single Curly Quotes
  $search[]  = chr(226).chr(128).chr(152);
  $replace[] = "'";
  $search[]  = chr(226).chr(128).chr(153);
  $replace[] = "'";
 
  // Replace Smart Double Curly Quotes
  $search[]  = chr(226).chr(128).chr(156);
  $replace[] = '"';
  $search[]  = chr(226).chr(128).chr(157);
  $replace[] = '"';
 
  // Replace En Dash
  $search[]  = chr(226).chr(128).chr(147);
  $replace[] = '--';
 
  // Replace Em Dash
  $search[]  = chr(226).chr(128).chr(148);
  $replace[] = '---';
 
  // Replace Bullet
  $search[]  = chr(226).chr(128).chr(162);
  $replace[] = '*';
 
  // Replace Middle Dot
  $search[]  = chr(194).chr(183);
  $replace[] = '*';
 
  // Replace Ellipsis with three consecutive dots
  $search[]  = chr(226).chr(128).chr(166);
  $replace[] = '...';
 
  // Apply Replacements
  $string = str_replace($search, $replace, $string);
 
  // Remove any non-ASCII Characters
  $string = preg_replace("/[^\x01-\x7F]/","", $string);
 
  return $string; 
}

?>

