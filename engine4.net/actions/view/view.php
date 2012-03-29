<?php
/*
 * I am the view action
 * It is my job to load content, if there is not already some loaded.
 * I make sure the content is there so that it can be rendered.
 */

function e4_action_view_view_go(&$data){
	
	if (!isset($data['configuration']['renderers']['all']['templates'][0])){
            if (sizeof(@$data['page']['body']['content']) == 0){
                $criteria = array();
                // TODO: Move this to index.php's main search function!
                // if (isset($_REQUEST['e4_tag'])){$criteria['tags'] = $_REQUEST['e4_tag'];}
                e4_data_search($criteria);
            }
                       
            
            switch (sizeof($data['page']['body']['content'])){
                    case 0: 
                        $data['configuration']['renderers']['all']['templates'][0] = '404.php'; 
                        e4_trace('Action VIEW selected template 404 for no items'); 
                        break;

                    case 1: 
                        $data['configuration']['renderers']['all']['templates'][0] = '?'; 
                        e4_trace('Action VIEW selected template ? for single item'); 
                        include e4_findinclude('actions/view/viewtype/view.php');
                        $data['renders']['all']['viewtype'] = 'view';
                        e4_trace('Action VIEW included viewtype view.php');
                        break;

                    default: 
                        // See if there is a viewtype for our current action
                        if (isset($_REQUEST['e4_action'])){
                            if (e4_findinclude('actions/view/viewtype/' . $_REQUEST['e4_action'] . '.php') !== 'void.php'){
                                include e4_findinclude('actions/view/viewtype/' . $_REQUEST['e4_action'] . '.php');
                                $data['renders']['all']['viewtype'] = $_REQUEST['e4_action'];
                                e4_trace('Action VIEW included viewtype ' . $_REQUEST['e4_action'] . '.php');
                            } else {
                                include e4_findinclude('actions/view/viewtype/search.php');
                                $data['renders']['all']['viewtype'] = 'search';
                                e4_trace('Action VIEW included viewtype search.php for generic multi item page');
                            }
                        } else {
                            // Treat an undefined page as the homepage.
                            // It is impossible for any other un-actioned page to generate multiple items (isn't it?)
                            include e4_findinclude('actions/view/viewtype/home.php');
                            $data['renders']['all']['viewtype'] = 'search';
                            e4_trace('Action VIEW included action home.php for home page');
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