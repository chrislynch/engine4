<?php

// $db = new PDO("sqlite:_custom/_default/data/f.db");
if ($this->p == '') {
	$posts = $this->_db->query('SELECT * FROM things WHERE Status = 1 ORDER BY Timestamp DESC LIMIT 10');
} else {
	// Check for a direct match on the URI
	$posts = $this->_db->query('SELECT * FROM things WHERE Status = 1 AND URI = "' . $this->p . '"');
	if($posts->rowcount() == 0){
		// No matching post. Try a search instead.
		
	}
}

while ($post = $posts->fetch()){
	_cms::loadThing($post);
	print $post['Name'] . "<br>";
}

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

?>
