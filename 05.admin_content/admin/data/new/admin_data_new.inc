<?php

$types = $this->_cms->loadtypes();

foreach($types as $type => $typeDescription){
	print "<h2>$type</h2>";
	// Print description
	if($type == 'Default'){
		print "<a href='admin/data/edit?ID=0'>Create new $type</a><br>";
	} else {
		print "<a href='admin/data/edit?ID=0&type=$type'>Create new $type</a><br>";
	}
	
}

?>