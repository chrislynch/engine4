<html>
<head>
	<base href="<?= $this->_basehref() ?>">
		
	<!-- JQuery -->
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
		
	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js"></script>
	
	<!-- Our stylesheet - overrides -->
	<link href="style.css" rel=stylesheet type="text/css">
	
</head>
<body>
	<h1><?php print $this->content->title;?></h1>
	<?php print @$this->content->html; print @$this->app->html; ?>
	<?php
		foreach($this->app->posts as $post){
			print "<h2>" . $post['Name'] . "</h2>";
			print @$post['HTML'];
		}
	?>
</body>
</html>



	
	




