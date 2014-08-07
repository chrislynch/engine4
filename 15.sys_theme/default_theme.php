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
	<?php 
		if (strlen(@$this->content->html) > 0){
			print $this->content->html;
		} else {
			if(sizeof($this->sys_content->posts) == 1){
				print_post($this->sys_content->posts[0]);
			} else {
				print_posts($this->sys_content->posts);
			}
			// print $this->sys_content->html;
		}
	?>
</body>
</html>

<?php

function print_posts($posts){
	$cols = 4;
	$rows = -1;
	$colmd = 12/$cols;
	$colcount = 0;
	foreach($posts as $post){
		if($colcount == 0) { print "<div class='row'>";}
		print "
<div class='col-md-$colmd'>
	{$post->dir}<br>
	<a href='{$post->content->url}'>
	<img src='http://www.cwlynch.co.uk/wp-content/uploads/2014/07/18uzfo6h34q9tjpg-300x168.jpg' width='100%'>
	<h2>{$post->content->title}</h2>		
	</a>
</div>";
		$colcount++;
		if($colcount == $cols) { print "</div>"; $colcount = 0; $rows--;}
		if($rows == 0) { break; }
	}
}

function print_post($post){
	print "POST!";
}

	
	



?>
