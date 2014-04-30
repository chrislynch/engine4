<html>
<head>
	<base href="<?= $this->_basehref() ?>">
	<link href="style.css" rel=stylesheet type="text/css">
</head>
<body>
	<h1><?php print $this->content->title;?></h1>
	<?php 
		if (strlen(@$this->content->html) > 0){
			print $this->content->html;
		} else {
			print $this->default_content->html;
		}
	?>
</body>
</html>
