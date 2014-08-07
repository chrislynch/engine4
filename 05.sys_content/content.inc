<?php

$this->sys_content->posts = array();

if($this->p !== '' && $this->p !== $this->_basedir()){
	$this->sys_content->posts = array_merge($this->sys_content->posts,outputContentDir($this->p));
} else {
	$dirs = scandir('10.content/posts/');
	print_r($dirs);
	foreach($dirs as $dir){
		if ($dir !== '.' && $dir !== '..') {
			if(substr($dir,0,1) !== '_'){
				$this->sys_content->posts = array_merge($this->sys_content->posts,outputContentDir($dir));
			}
		}
	}
}


function outputContentDir($dir){
	print '10.content/posts/' . $dir . "<br>";
	$subdirs = scandir('10.content/posts/' . $dir,SCANDIR_SORT_DESCENDING);
	$return = array();
	foreach($subdirs as $subdir){
		if ($subdir !== '.' && $subdir !== '..') {
			$sube = new e();
			$sube->_open("10.content/posts/$dir/$subdir");
			$sube->dir = $dir;
			$return[] = $sube;
		}
	}
	return $return;
}

?>
