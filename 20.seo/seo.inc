<?php

/* TITLE */

// Set the default title based on the config
$this->seo->title = $this->sys->config['Name'];

// Override the title with the posts loaded from app
if(sizeof($this->app->posts) == 1){
	$posts = $this->app->posts;
	$post = array_shift($posts);
	$this->seo->title = strlen($post['Title']) > 0 ? $post['Title'] : $post['Name'];
} elseif (sizeof($this->app->posts) > 1) {
	if ($this->p !== '') {
		// We are in a subdirectory
		$this->seo->title = ucwords(trim(@$this->parray(1) . " " . @$this->parray(0) . " Archive"));
	}
}

/* META DESCRIPTION */



/* META KEYWORDS */



?>
