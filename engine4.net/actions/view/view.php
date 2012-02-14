<?php
/*
 * I am the view action
 * It is my job to load content, if there is not already some loaded.
 * I make sure the content is there so that it can be rendered.
 */

function e4_action_view_view_go(&$data){
	/*
	 * All views and actions should start by loading up their defaults, if they are not already there.
	 */
	
	/*
	 * If another action has not already loaded up content, we need to load it up 
	 */
	if (sizeof(@$data['page']['content'] == 0)){
		e4_data_search(array());	
	}
	
	/*
	 * Now that we know what is happening in terms of data, we can decide what templates to use.
	 * @todo We need a way to tell the system not to do this, if a previous action has already selected a template
	 */
	if (!isset($data['configuration']['renderers']['all']['templates'][0])){
		if (isset($_REQUEST['e4_ID']) && $_REQUEST['e4_ID'] > 0){
			e4_trace('Action VIEW selected template ? for single item');
			$data['configuration']['renderers']['all']['templates'][0] = '?';	
		} else {
			switch (sizeof($data['page']['body']['content'])){
				case 0: $data['configuration']['renderers']['all']['templates'][0] = '404.php'; e4_trace('Action VIEW selected template 404 for no items'); break;
				case 1: $data['configuration']['renderers']['all']['templates'][0] = '?'; e4_trace('Action VIEW selected template ? for single item'); break;
				default: 
					if (isset($_REQUEST['e4_search'])){
						include_once e4_findinclude('actions/view/viewtype/search.php');
						e4_trace('Action VIEW included template search.php for multiple items');
					} else {
						include_once e4_findinclude('actions/view/viewtype/home.php');
						e4_trace('Action VIEW included action home.php for home page');
					}
			}
		}	
	}
	
	/*
	 * After the content has been loaded, we need to parse it and set up any page level data that is affected by it.
	 * If there is no content, we need to perform a redirect or do something else.
	 */
	
	if(sizeof($data['page']['body']['content']) == 0){
		/*
		 * We did not find anything to show to the user
		 * @todo Handle 404 errors, redirect errors, etc. 
		 */	
	} else {
		/*
		 * SEO Meta Data - Scan all content items and aggregate the SEO data
		 * If there is only one item, use its keywords
		 */
		if(sizeof($data['page']['body']['content']) > 1){
			$keywords = array();
			foreach($data['page']['body']['content'] as $content){
				/* 
				 * Count keywords against each item of content and then select the X most popular
				 */
				$contentkeywords = explode(',',@$content['data']['seo']['keywords']);
				foreach($contentkeywords as $keyword) {
					if (isset($keywords[$keyword])){
						$keywords[$keyword] += 1;
					} else {
						$keywords[$keyword] = 1;
					}
				}
			
				
			}
			
			// Sort the keywords array from high to low frequency
			arsort($keywords);	
			$pagekeywords = array();
			foreach ($keywords as $keyword => $keywordscore){
				if (strlen($keyword) > 0) {	$pagekeywords[] = $keyword; }
				if (sizeof($pagekeywords) == $data['configuration']['seo']['keywords']['count']){ break; }	
			}
			
			// Implode the resulting array to create a string of keywords specific to this page.
			$data['page']['head']['keywords'] = implode(',',$pagekeywords);	
		} else {
			// Read the first item from the array and use its data
			foreach($data['page']['body']['content'] as $content){
				if (strlen(@$content['data']['seo']['abstract']) > 0){
					$data['page']['head']['abstract'] = $content['data']['seo']['abstract'];	
				}
				if (strlen(@$content['data']['seo']['description']) > 0){
					$data['page']['head']['description'] = $content['data']['seo']['description'];	
				}
				if (strlen(@$content['data']['seo']['keywords']) > 0){
					$data['page']['head']['keywords'] = $content['data']['seo']['keywords'];	
				}
				break;
			}
		}
		/*
		 * SEO Meta Data Complete
		 */
	}	
}


?>