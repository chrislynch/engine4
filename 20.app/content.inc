<?php

// Set up pager
$this->app->pages = 1;
if(isset($_GET['page'])) { $this->app->page = $_GET['page']; } else { $this->app->page = 1; }

if ($this->p == '') {
	$start = 10 * ($this->app->page -1 );
	$end = 10 * ($this->app->page);
	$things = $this->_db->query("SELECT * FROM things WHERE Status = 1 AND System = 1 ORDER BY Timestamp DESC LIMIT $start,$end");
	$this->app->pages = ceil($this->_db->result('SELECT COUNT(0) FROM things WHERE Status = 1 AND ID > 1') / 10);
} else {
	// Check for a direct match on the URI
	$things = $this->_db->query('SELECT * FROM things WHERE Status = 1 AND URI = "' . $this->p . '"');
	if($things->fetchColumn() === FALSE){
		// No matching post. Try a search instead.
		$search = $this->parray(0);
		$searchfor = $this->parray(1);
		if ($search !== '' && $searchfor !== ''){
			$start = 10 * ($this->app->page -1 );
			$end = 10 * ($this->app->page);
			$things = $this->_db->query("SELECT t.* FROM things t
							JOIN things_data td ON td.ID = t.ID
							WHERE td.Field = '$search' AND td.Value LIKE '%$searchfor%'
							AND t.Status = 1 AND System >= 0 AND t.ID > 1 ORDER BY t.Timestamp DESC LIMIT $start,$end");

			$this->app->pages = ceil($this->_db->result("SELECT COUNT(0) FROM things t
							JOIN things_data td ON td.ID = t.ID
							WHERE td.Field = '$search' AND td.Value LIKE '%$searchfor%'
							AND t.Status = 1 AND System >= 0 AND t.ID > 1") / 10);
		}
	} else {
		// For now, just pick it up again. A bit annoying but there you are!
		$things = $this->_db->query('SELECT * FROM things WHERE Status = 1 AND URI = "' . $this->p . '"');
	}
}

$this->app->posts = array();

while ($thing = $things->fetch()){
	_cms::loadThing($thing);
	_cms::renderThing($thing);
	$this->app->posts[$thing['ID']] = $thing;
}


?>