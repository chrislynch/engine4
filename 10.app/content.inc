<?php

// Set up pager
$this->app->pages = 1;
if(isset($_GET['page'])) { $this->app->page = $_GET['page']; } else { $this->app->page = 1; }

if ($this->p == '') {
	$start = 10 * ($this->app->page -1 );
	$end = 10 * ($this->app->page);
	$things = $this->_db->query("SELECT * FROM things WHERE Status = 1 AND ID > 1 ORDER BY Timestamp DESC LIMIT $start,$end");
	$this->app->pages = ceil($this->_db->result('SELECT COUNT(0) FROM things WHERE Status = 1 AND ID > 1') / 10);
} else {
	// Check for a direct match on the URI
	$things = $this->_db->query('SELECT * FROM things WHERE Status = 1 AND URI = "' . $this->p . '"');
	if($things->columnCount() == 0){
		// No matching post. Try a search instead.
		$search = $this->parray(0);
		$searchfor = $this->parray(1);
		if ($search !== '' && $searchfor !== ''){
			$start = 10 * ($this->app->page -1 );
			$end = 10 * ($this->app->page);
			$things = $this->_db->query("SELECT t.* FROM things t
							JOIN things_data td ON td.ID = t.ID
							WHERE td.Field = '$search' AND td.Value LIKE '%$searchfor%'
							AND t.Status = 1 AND t.ID > 1 ORDER BY t.Timestamp DESC LIMIT $start,$end");

			$this->app->pages = ceil($this->_db->result("SELECT COUNT(0) FROM things t
							JOIN things_data td ON td.ID = t.ID
							WHERE td.Field = '$search' AND td.Value LIKE '%$searchfor%'
							AND t.Status = 1 AND t.ID > 1") / 10);
		}
	}
}

$this->app->posts = array();

while ($thing = $things->fetch()){
	_cms::loadThing($thing);
	_cms::renderThing($thing);
	$this->app->posts[$thing['ID']] = $thing;
}

/*
if(sizeof($this->app->posts) == 1 && !$directhit){
	$thing = array_shift($this->app->posts);
	$this->_goto($thing['URI']);
}
*/
/*
function loadThing(&$thing) {
	global $e;
	// Load all of the data associated with a thing.
	$postdatafields = $e->_db->query("SELECT * FROM things_data WHERE ID = ${thing['ID']}");
	while($postdata = $postdatafields->fetch()){
		foreach($postdata as $field => $value){
			$thing[$field] = $value;
		}
	}

}
*/
?>
