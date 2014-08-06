<?php

if($this->p !== '' && $this->p !== $this->_basedir()){
	outputContentDir($this->p);
} else {
	$dirs = scandir('10.content');
	foreach($dirs as $dir){
		if ($dir !== '.' && $dir !== '..') {
			if(substr($dir,0,1) !== '_'){
				outputContentDir($dir);
			}
		}
	}
}


function outputContentDir($dir){
	$subdirs = scandir('10.content/' . $dir,SCANDIR_SORT_DESCENDING);
	$cols = 4;
	$rows = 1;
	$colmd = 12/$cols;
	$colcount = 0;
	foreach($subdirs as $subdir){
		if ($subdir !== '.' && $subdir !== '..') {
			$sube = new e();
			$sube->_open("10.content/$dir/$subdir");
			if($colcount == 0) { print "<div class='row'>";}
			print "
	<div class='col-md-$colmd'>
		$dir
		<a href='{$sube->content->url}'>
		<img src='http://www.cwlynch.co.uk/wp-content/uploads/2014/07/18uzfo6h34q9tjpg-300x168.jpg' width='100%'>
		<h2>{$sube->content->title}</h2>		
		</a>
	</div>
	";
			$colcount++;
			if($colcount == $cols) { print "</div>"; $colcount = 0; $rows--;}
			if($rows == 0) { break; }
		}
	}
	if($colcount !== 0) { print "</div>"; }
}

?>
