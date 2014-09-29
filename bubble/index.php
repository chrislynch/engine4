<?php
$textareatext = '';
foreach($_FILES as $field => $file){
	if($file['error'] == 0){
		$textareatext = file_get_contents($file['tmp_name']);
	} else {
		
	}
}


?>

<html>
<head>
	<link href='http://fonts.googleapis.com/css?family=Droid+Sans:400,700' rel='stylesheet' type='text/css'>
	<style>
		body{font-family: 'Droid Sans', sans-serif;}	
		h1 {margin-top: 0px; font-size: 14pt;}
		h2 {margin-top: 0px; font-size: 12pt; color: darkred;}
		#left {float: left; width: 70%; min-width: 360px; text-align: center;}
		#left #texta {width: 100%; height: 90%;}
		#right {float: left; width: 30%;}

		.inner {padding-left: 10px;}
	</style>
</head>
<body>
	<form action="/bubble/output/bubble_app_output.php" method="POST" target="_blank">
	<div id="left">		
			<textarea id="texta" name="bubbletext" rows="20" cols="75"><?php print $textareatext; ?></textarea><br><br>
			<!-- <label>Convert dialogue to upper-case</label>: <input type="checkbox" name="param_uppercasedialogue"><br><br> -->
			<input type="submit" name="op" value="Convert text to comic book script">


		
	</div>
	<div id="right">
		<div class='inner'>
			<h1>Bubble: Plain Text Markup for Comic Book Scripts</h1>
			<h2>Getting Started</h2>
			<p>Copy and paste your script into the text editor opposite and click <strong>Convert text to comic book script</strong>. Your script will be output in a new window as a HTML page that you can copy and paste, save, or print.</p>
			<h2>Formatting Basics:</h2>
			<p># at the start of the line denotes a new page.</p>
			<p>## at the start of a line denotes a new panel.</p>
			<p>tab or one space at the start of a line denotes a character name.</p>
			<p>2 tabs or two spaces at the start of the line denote dialogue or parenthetical for dialogue.</p>
			<h2>Cover Pages:</h2>
			<p>Anything at the top of your script <em>before</em> the first page marker (#) will be treated as content for cover page and formatted accordingly.</p>
			<h2>Bold and Italics</h2>
			<p>Surround text with * to make it italic.</p>
			<p>Surround text with ** to make it bold.</p>
			<h2>Private and Public Comments</h2>
			<p>Start a line with ! to mark it as a comment.</p>
			<p>Start a line with a . to mark it as a private comment, which will then be excluded from the rendered script.</p>
			<h2>Read More</h2>
			<p>For more information on Bubble, go to the <a href='/bubble'>official Bubble pages</a></p>
			<hr>
			<h2>File Management</h2>
				<strong>Save your script as a TXT file<br>
				<input type="submit" name="op" value="Save script as TXT">
			</form>
			<br><br>
			<form action="/bubble/index.php" method="POST" enctype="multipart/form-data">
				<strong>Upload a TXT file:</strong><br>
				<input name='upload' type='file'><br>
				<input type="submit" name="op" value="Upload script as TXT">
			</form>
		</div>
	</div>
	
	
</body>
</html>
