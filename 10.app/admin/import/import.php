<?php

$postdirs = scandir("_custom/_default/content/post/");
foreach ($postdirs as $postdir) {
	foreach($postdirs as $postdir){
		if($postdir !== '.' && $postdir !== '..'){
			$post = new e();
			$post->_open("_custom/_default/content/post/$postdir");
			print "<pre>";
			print_r($post);
			print "</pre>";
			/*
			foreach($postfiles as $postfile){
				$tabledata = array();			
				$tabledata['ID'] = 0;
				$tabledata['Name'] = 	
				$this->_db->replaceinto('things',$tabledata);
			}
			*/
		}
		
	}
}

?>