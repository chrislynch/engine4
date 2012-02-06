<?php
/*
 * I am the view action
 * It is my job to load content, if there is not already some loaded.
 * I make sure the content is there so that it can be rendered.
 */

/*
 * All views and actions should start by loading up their defaults, if they are not already there.
 */

// TODO: This should not load anything if there has already been data loaded.
e4_data_search(array());

/*
 * After the content has been loaded, we need to parse it and set up any page level data that is affected by it.
 */

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

?>