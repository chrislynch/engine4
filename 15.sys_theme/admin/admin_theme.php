
<html>
<head>
	<base href="<?= $this->_basehref() ?>">
</head>
<body>
	<a href="admin/data/new">New Item</a>&nbsp;&nbsp;
	<a href="admin/data/">Content List</a>&nbsp;&nbsp;
	<?php print $this->admin_content->html; ?>
</body>
</html>

<?php exit(); // The last thing we do in admin theme is to exit, preventing anything else from running ?>