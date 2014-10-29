<?php

// Set up pager
$this->app->pages = 1;
$pagesize = 10;
$relatedthings = FALSE; // Assume that we are not picking up related things under we are certain we have one item
if(isset($_GET['page'])) { $this->app->page = $_GET['page']; } else { $this->app->page = 1; }

if ($this->p == '') {
	
	$start = $pagesize * ($this->app->page -1 );
		
	$things = $this->_db->query("SELECT * FROM things WHERE Status = 1 AND System = 1 ORDER BY Timestamp DESC LIMIT $start,$pagesize");
	$this->app->pages = ceil($this->_db->result('SELECT COUNT(0) FROM things WHERE Status = 1 AND ID > 1') / $pagesize);
} else {
	// Check for a direct match on the URI
	$things = $this->_db->query('SELECT * FROM things WHERE Status = 1 AND URI = "' . $this->p . '"');
	if($things->fetchColumn() === FALSE){
		// No matching post. Try a search instead.
		$search = $this->parray(0);
		$searchfor = $this->parray(1);
		if ($search !== '' && $searchfor !== ''){
			$start = $pagesize * ($this->app->page -1 );
			$end = $pagesize * ($this->app->page);
			if ($search == 'Type'){
				$thingsSQL = "SELECT t.* FROM things t
						WHERE Type = '$searchfor'
						AND t.Status = 1 AND System >= 0 AND t.ID > 1 ORDER BY t.Timestamp DESC LIMIT $start,$pagesize";
				$pageSQL = "SELECT t.* FROM things t
						WHERE Type = '$searchfor'
						AND t.Status = 1 AND System >= 0 AND t.ID > 1";
			} else {
				$thingsSQL = "SELECT t.* FROM things t
							JOIN things_data td ON td.ID = t.ID
							WHERE td.Field = '$search' AND td.Value LIKE '%$searchfor%'
							AND t.Status = 1 AND System >= 0 AND t.ID > 1 ORDER BY t.Timestamp DESC LIMIT $start,$pagesize";
				$pageSQL = "SELECT COUNT(0) FROM things t
							JOIN things_data td ON td.ID = t.ID
							WHERE td.Field = '$search' AND td.Value LIKE '%$searchfor%'
							AND t.Status = 1 AND System >= 0 AND t.ID > 1";
			}

			$things = $this->_db->query($thingsSQL);
			$this->app->pages = ceil($this->_db->result($pageSQL) / $pagesize);
		}
	} else {
		// For now, just pick it up again. A bit annoying but there you are!
		$things = $this->_db->query('SELECT * FROM things WHERE Status = 1 AND URI = "' . $this->p . '"');
		$relatedthings = TRUE;
	}
}

$this->app->posts = array();

if(is_object($things)){
	while ($thing = $things->fetch()){
		_cms::loadThing($thing);
		_cms::renderThing($thing);
		if ($relatedthings && sizeof($thing['_meta']['tags']) > 0) {
			$fields = _cms::loadfields($thing['Type']);
			$thingsSQL = "SELECT t.* FROM things t ";
			$tdcount = 0;
			foreach($fields as $field){
				if($field['Related'] == TRUE) {
					// for($i = 1; $i <= sizeof($thing['_meta']['tags']); $i++) {
						$tdcount++;
						$thingsSQL .= "JOIN things_data td$tdcount ON td$tdcount.ID = t.ID AND td$tdcount.Field = '{$field['FieldName']}' ";
					// }
				}
			}
			$thingsSQLWhere = array();
			foreach($thing['_meta']['tags'] as $tag){
				for($i = 1; $i <= $tdcount; $i++) {
					$thingsSQLWhere[] = "td$i.Value LIKE '%$tag%'";
				}	
			}
			$thingsSQL .= "WHERE (";
			$thingsSQL .= implode(" OR ",$thingsSQLWhere);
			$thingsSQL .= ") AND (t.Status = 1 AND System >= 0 AND t.ID > 1 AND t.ID <> {$thing['ID']})
					ORDER BY ABS(t.ID - {$thing['ID']}) DESC LIMIT $pagesize";

			$things = $this->_db->query($thingsSQL);
			$thing['Related'] = array();
			if(is_object($things)){
				while ($relatedThing = $things->fetch()){
					_cms::loadThing($relatedThing);
					_cms::renderThing($relatedThing);
					$thing['Related'][] = $relatedThing;
				}
			}
		}

		$this->app->posts[$thing['ID']] = $thing;
	}
}

/*
* LOAD RELATED ITEMS
*/

/*
* LOAD BLOCKS
*/
$things = $this->_db->query("SELECT * FROM things WHERE Status = 1 AND Type = 'block' ORDER BY Timestamp DESC");

$this->app->blocks = array();

while ($thing = $things->fetch()){
	_cms::loadThing($thing);
	_cms::renderThing($thing);
	$this->app->blocks[$thing['ID']] = $thing;
}

?>
