
<html>
<head>
	<base href="<?= $this->_basehref() ?>">
</head>
<body>
	<h1><?php print $this->admin_content->title;?></h1>
	<?php print $this->admin_content->html; ?>
</body>
</html>

<?php exit(); // The last thing we do in admin theme is to exit, preventing anything else from running ?>