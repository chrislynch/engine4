<h1>New Content</h1>
<p>Please select a type for the new content item. The type of an item cannot be changed after it has been created.</p>

<?php
	$types = _cms::loadtypes();
	foreach($types as $type){
		print("<h3><a href='admin/data/edit?type=$type'>$type</a></h3>");
	}
?>
