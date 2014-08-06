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
	$subdirs = scandir('10.content/' . $dir);
	foreach($subdirs as $subdir){
		if ($subdir !== '.' && $subdir !== '..') {
			$sube = new e();
			$sube->_open("10.content/$dir/$subdir");
			print "
	<div class='col-md-4'>
		$dir
		<a href='{$sube->content->url}'>
		<h2>{$sube->content->title}</h2>
		</a>
	</div>
	";
		}
	}
}

?>
