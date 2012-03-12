<?php
/*
 * This is the primary index file for an e4 installation.
 * It will bootstrap the system, making the initial database connection etc. and then 
 * it works out what action and view we are using, calls that action, then passes the resulting data over to the view to be rendered.
 */
include 'engine4.net/bootstrap.php';
include e4_findinclude('config.php');

$db = mysql_connect($data['configuration']['database']['server'],
					$data['configuration']['database']['username'],
					$data['configuration']['database']['password']);
mysql_select_db($data['configuration']['database']['schema'],$db);

/*
 * ACTIONS
 * We will perform a series of actions in order to load up the data array, ready for the render,
 * and to do any tasks that we are required to do.
 * At the end of everything, we always run the "view" action (or *do* we?)
 */

e4_action_search();

// Run the security action, to ensure that we are not allowing a user to run actions they are not entitled to
include_once e4_findinclude('actions/security/security.php');
e4_action_security_security_go($data);

// Include the analytics code, so that any action can record a stat
include_once e4_findinclude('actions/analytics/analytics.php');

if (!e4_redirect()){
    foreach($data['actions'] as $action){
	include_once e4_findinclude('actions/' . $action);
	e4_trace('Looking for function ' . e4_getGoFunction($action,'action'));
	if (function_exists(e4_getGoFunction($action,'action'))){
		e4_trace('Found function ' . e4_getGoFunction($action,'action'));
		$parameters = array( &$data );
		call_user_func_array(e4_getGoFunction($action,'action'),$parameters);
	} else {
		e4_trace('Could not find function ' . e4_getGoFunction($action,'action'));
	}
    }
}

// Record generic stats
e4_action_analytics_analytics_go($data);

/*
 * Only actions can re/write the DB. Close the connection now.
 */
mysql_close($db);

/*
 * The result of all our processing *could* be a redirect.
 * Run the redirection function in case this is the case.
 * If it returns false, there is no redirect and we need OUTPUT.
 */
if (! e4_redirect()){
	/*
	 * RENDERERS
	 * Renders turn the results of actions in outputs. 
	 * The standard renderer is HTML, but others could be built to server other platforms.
	 */
	foreach($data['renderers'] as $renderer){
		include_once e4_findinclude('renderers/' . $renderer);
		e4_trace('Looking for function ' . e4_getGoFunction($renderer,'renderer'));
		if (function_exists(e4_getGoFunction($renderer,'renderer'))){
			e4_trace('Found function ' . e4_getGoFunction($renderer,'renderer'));
			call_user_func(e4_getGoFunction($renderer,'renderer'),e4_prepareTemplates());	
		} else {
			e4_trace('Could not find function ' . e4_getGoFunction($renderer,'renderer'));
		}
	}

	/*
	 * Output debugging information
	 */
	if (isset($_REQUEST['debug']) || $data['configuration']['debug']){
		print '<hr><pre>' . htmlentities(print_r($data,TRUE)) . '</pre>';
		print '<hr><pre>' . htmlentities(print_r($_REQUEST,TRUE)) . '</pre>';	
	}
}

// 

/*
 * CONTENT DATA FUNCTIONS
 * These functions load up content and data for the system to use.
 * Lots of actions have cause to do this, so the functions live here for all to share. 
 */

function e4_data_new(){
	/*
	 * Create a new data item array and return it for use.
	 */
	$newitem = array('ID'=>0,'name'=>'','type' => '', 'url' => '', 'folder' => '', 'iscontent' => 1, 'status' => 1);
        $newitem['data'] = array();
        $newitem['linkages'] = array();
        $newitem['tags'] = array();
	return $newitem;
}

function e4_data_load($ID,$addToData = TRUE,$loadLinkages = TRUE){
	/*
	 * Perform a simple load of a single datacontent ID
	 */
	global $db;
	global $data;
	
	$newdata = array();
	$dataquery = e4_db_query("SELECT ID,Name,Type,URL,Folder,Timestamp,Data,is_content,status FROM e4_data WHERE ID = $ID");
	
	while($datarecord = mysql_fetch_assoc($dataquery)){
		// These are the header items. Make sure we have these and that they have the right name & case.
		$newdata['ID'] = $datarecord['ID'];
		$newdata['name'] = $datarecord['Name'];
		$newdata['type'] = $datarecord['Type'];
		$newdata['url'] = $datarecord['URL'];
                if (strlen($newdata['url']) > 0){
                    $newdata['link'] = $newdata['url'];
                } else {
                    $newdata['link'] = '@@configuration.basedir@@?e4_ID=' . $newdata['ID'];
                }
                $newdata['folder'] = $datarecord['Folder'];
		$newdata['timestamp'] = $datarecord['Timestamp'];
		$newdata['iscontent'] = $datarecord['is_content'];
		$newdata['status'] = $datarecord['status'];
		// Everything is saved in [data]. Jump down in [data][data] to get what we need, if it exists somehow.  
		$newdata['data'] = unserialize(base64_decode($datarecord['Data']));
		if(isset($newdata['data']['data'])){
			$newdata['data'] = $newdata['data']['data'];
		}
		// Unlikely that we will need XML, so have removed it from the query above and in the line below.
		// $data['page']['body']['content'][$datarecord['ID']]['xml'] = $datarecord['XML'];
	}
        
        $newdata['linkages'] = array();
        $linkagequery = e4_db_query('SELECT ID,LinkType,LinkID FROM e4_linkage WHERE ID = ' . $ID);
        
        while($linkagerecord = mysql_fetch_assoc($linkagequery)){
            if ($loadLinkages === TRUE){
                $newdata['linkages'][$linkagerecord['LinkType']][] = e4_data_load($linkagerecord['LinkID'], FALSE, FALSE);
            } else {
                $newdata['linkages'][$linkagerecord['LinkType']][] = $linkagerecord['LinkID'];
            }
        }
        
        $newdata['tags'] = array();
        $tagquery = e4_db_query('SELECT TagType,Tag FROM e4_tags WHERE ID = ' . $ID);
        while($tagrecord = mysql_fetch_assoc($tagquery)){
            if(!isset($newdata['tags'][$tagrecord['TagType']])){ $newdata['tags'][$tagrecord['TagType']] = array(); }
            $newdata['tags'][$tagrecord['TagType']][] = $tagrecord['Tag'];
        }
	
        if ($addToData){
            $data['page']['body']['content'][$newdata['ID']] = $newdata;
            $data['page']['body']['contentByType'][$newdata['type']][$newdata['ID']] = $newdata;
            $data['page']['body']['contentByFolder'][$newdata['folder']][$newdata['ID']] = $newdata;
        }
        
	return $newdata;
}

function e4_data_save($saveData){
	/*
	 * Save a piece of data back to the DB
	 */
	global $db;
        global $data;
	
	if (isset($saveData['ID'])){
		$saveID = $saveData['ID'];
	} else {
		$saveID = 0;
	}
	$serialisedSaveData = base64_encode(serialize($saveData));
	$xmlData = e4_xmlify($saveData);
	
	if (strlen($saveData['name']) > 0 AND strlen($saveData['type']) > 0){
		$saveQuery = 'INSERT INTO e4_data 
                            SET  ID = ' . $saveID . ',
                                        Name = "' . $saveData['name'] . '",
                                        Type = "' . $saveData['type'] . '",
                                        URL = "' . $saveData['url'] . '",
                                        Folder = "' . $saveData['folder'] . '",
                                        Data = "' . mysql_escape_string($serialisedSaveData) . '",
                                        XML  = "' . $xmlData . '",
                                        is_content = ' . $saveData['iscontent'] . ',
                                        status = ' . $saveData['status'] . '
                        ON DUPLICATE KEY UPDATE 
                                        Name = "' . $saveData['name'] . '",
                                        Type = "' . $saveData['type'] . '",
                                        URL = "' . $saveData['url'] . '",
                                        Folder = "' . $saveData['folder'] . '",
                                        Data = "' . mysql_escape_string($serialisedSaveData) . '",
                                        XML  = "' . $xmlData . '",
                                        is_content = ' . $saveData['iscontent'] . ',
                                        status = ' . $saveData['status'];
		// Run the query through our traced query function
		e4_db_query($saveQuery); 
		// If this was an insert using the next available ID, return that ID rather than the ID given
		if ($saveID == 0){ 
                    $saveID = mysql_insert_id($db);
                    $saveData['ID'] = $saveID;
                }
		// Display a message
                e4_trace('User ' . $data['user']['ID'] . ' saved record '. $saveID,TRUE);
		e4_message('Saved record ' . $saveID,'Success');
                
                // If we have an ID, we can save any linkages that we have.
                e4_db_query('DELETE FROM e4_linkage WHERE ID = ' . $saveData['ID']);
                if(isset($saveData['linkages'])){
                    foreach($saveData['linkages'] as $linkType=>$linkID){
                        if (is_array($linkID)){
                            foreach($linkID as $iLinkID){
                                e4_db_query('INSERT INTO e4_linkage SET 
                                        ID = ' . $saveData['ID'] . ', 
                                        LinkType = "' . $linkType . '",
                                        LinkID = ' . $ilinkID);
                            }
                        } else {
                            e4_db_query('INSERT INTO e4_linkage SET 
                                        ID = ' . $saveData['ID'] . ', 
                                        LinkType = "' . $linkType . '",
                                        LinkID = ' . $linkID);
                        }
                    }
                }
                
                // Save tags
                e4_db_query('DELETE FROM e4_tags WHERE ID = ' . $saveData['ID']);
                if(isset($saveData['tags'])){
                    foreach($saveData['tags'] as $TagType=>$TagValues){
                        foreach($TagValues as $tagvalue){
                            e4_db_query('INSERT INTO e4_tags SET 
                                            TagType = "' . $TagType . '",
                                            Tag = "' . $tagvalue . '",
                                            ID = ' . $saveData['ID']);
                        }
                    }
                }
                
		// Return the ID of the saved record.
		return $saveID;	
	}
	
}

function e4_action_search(){
	/*
	 * Check the URL and other parameters to find our actions
	 */
	global $data;
	      
	if (isset($_REQUEST['e4_url']) AND !(isset($_REQUEST['e4_ID']))){
            $findURLQuery = e4_db_query('SELECT ID FROM e4_data WHERE type="Action" 
                                          AND (URL = "' . $_REQUEST['e4_url'] . '" OR URL = "' . $_REQUEST['e4_url'] . '/")');
            if (mysql_num_rows($findURLQuery) == 1){
                $newaction = e4_data_load(mysql_result($findURLQuery, 0),FALSE);
                if (!isset($_REQUEST['e4_action'])){ $_REQUEST['e4_action'] = $newaction['data']['e4']['action']; }
                if (!isset($_REQUEST['e4_op'])){ $_REQUEST['e4_op'] = $newaction['data']['e4']['op']; }
                if (isset($newaction['data']['e4']['params'])){
                    $params = explode('&',$newaction['data']['e4']['params']);
                    foreach($params as $param){
                        $param = explode('=',$param);
                        if (!isset($_REQUEST[trim($param[0])])){
                            $_REQUEST[trim($param[0])] = trim($param[1]);
                        }
                    }
                }
            }
	}
	
        if(isset($_REQUEST['e4_action'])){
            if ($_REQUEST['e4_action'] !== 'security'){
                array_unshift($data['actions'],$_REQUEST['e4_action'] . '/' . $_REQUEST['e4_action'] . '.php');
            }
	} 
	
	// Once we have picked up the actions to run *before* view, we need to enforce mandatory pre-cursor actions
	// Security is already called before ANYTHING else, so that is no longer needed here.
	// @todo This is where any "modules", like eCommerce, would be loaded. 
	// array_unshift($data['actions'],'security/security.php');
}

function e4_data_search($criteria=array(),$addToData = TRUE,$onlyContent=TRUE,$suppressID=FALSE){
    global $data;
	/*
	 * Perform a search on the e4_data.
	 * @todo This might be the first function that is going to get long enough to be unweildy in this file - think on!
	 */
	global $db;
	$searchQuery = '';
	
	if (isset($_REQUEST['e4_action']) && $_REQUEST['e4_action'] = 'admin'){
		$admin=TRUE;
	} else {
		$admin=FALSE;	
	}
	
	/*
	 * Start with a URL lookup, if a URL has been specified
	 */
	if (isset($_REQUEST['e4_url']) AND !(isset($_REQUEST['e4_ID']))){
		$findURLQuery = e4_db_query('SELECT ID FROM e4_data WHERE URL = "' . $_REQUEST['e4_url'] . '" AND status <> 0 AND Type<>"Action"');
		if (mysql_num_rows($findURLQuery) == 1){
			$_REQUEST['e4_ID'] = mysql_result($findURLQuery, 0);
		} else {
			$_REQUEST['e4_ID'] = -1;
		}
	}
	
	/*
	 * Now build the real search query
	 */
	$searchQuery = '';
        $searchQueryJoin = '';
	$searchQueryCriteria = '';
	$searchQueryKeywords = array();
	$searchQueryHaving = '';
	$searchQueryOrderBy = '';
	
	if ($onlyContent){
		$searchQueryCriteria = ' is_content = 1 ';
	}
	
	if (is_array($criteria) && sizeof($criteria) > 0){
            foreach($criteria as $field=>$value){
                if (strtoupper($field) == 'XML'){
                    $searchQueryKeywords[]= '"' . $value . '"';
                } elseif(strtoupper($field) == 'TAGS'){
                    $searchQueryJoin = ' JOIN e4_tags ON e4_tags.ID = e4_data.ID AND e4_tags.tag = "' . $value . '" ';
                } else {
                    if(strlen($searchQueryCriteria) > 0) { $searchQueryCriteria .= ' AND '; }
                    $searchQueryCriteria .= ' ' . $field . ' = ';
                    if (is_numeric($value)){
                            $searchQueryCriteria .= $value . ' ';
                    } else {
                            $searchQueryCriteria .= '"' . $value . '" ';
                    }
                }
            }
	}
	if(isset($_REQUEST['e4_search'])){
		$searchQueryKeywords[] = '"' . $_REQUEST['e4_search'] . '"';
	}
	
	if(!$suppressID &&  isset($_REQUEST['e4_ID'])){
            // Find an item based on its ID
            $searchQuery = 'SELECT e4_data.ID FROM e4_data';
            if(isset($_REQUEST['e4_ID'])){
                    $searchQueryCriteria = 'ID =' . $_REQUEST['e4_ID'];
            }		
	} else {
		if(sizeof($searchQueryKeywords) > 0){
			// Perform a search of some type
			$searchQuery = 'SELECT e4_data.ID, MATCH(XML) AGAINST (' . implode(',',$searchQueryKeywords) . ' IN BOOLEAN MODE) AS score FROM e4_data';
			$searchQueryHaving = 'score > 0';
		} else {
			// Generic search that just goes looking for anything
			$searchQuery = 'SELECT e4_data.ID FROM e4_data';
		}	
	}

	// We build in the criteria afterwards to avoid duplication of code.
	// Search query needs to be extended for non-admin users/screens
	if (!$admin){
		if (strlen($searchQueryCriteria) > 0) {
			$searchQueryCriteria = ' (' . $searchQueryCriteria . ') AND '; 
		}
		$searchQueryCriteria .= ' status <> 0 ';
	}
        if (strlen($searchQueryJoin) > 0){
            $searchQuery .= $searchQueryJoin;
        }
	if (strlen($searchQueryCriteria) > 0){
		$searchQuery .= ' WHERE ' . $searchQueryCriteria;	
	}
	if (strlen($searchQueryHaving) > 0){
		$searchQuery .= ' HAVING ' . $searchQueryHaving;	
	}
	if (strlen($searchQueryOrderBy) > 0){
            $searchQuery .= ' ORDER BY ' . $searchQueryOrderBy;	
	} else {
            $searchQuery .= ' ORDER BY timestamp DESC';	
        }
	
        // Add in limits and take into account paging
        $pagerQuery = 'SELECT COUNT(0) FROM (' . $searchQuery . ') count';
        $pagerQuery = e4_db_query($pagerQuery);
        if ($pagerQuery){
            $pageCount = mysql_result($pagerQuery, 0);
            $data['page']['body']['pager']['recordcount'] = $pageCount;
            if ($pageCount < $data['configuration']['paging']['page-size']) {
                $pageCount = 1;
            } else {
                $pageCount = intval(1 + ($pageCount / $data['configuration']['paging']['page-size']));
            }
        } else {
            $pageCount = 0;
        }
        $data['page']['body']['pager']['pagecount'] = $pageCount;
        if ($pageCount > 0){
            
        }
        
        // Now adding the paging to the query
        if (isset($_REQUEST['e4_page'])){
            $pageStart = $_REQUEST['e4_page'] * $data['configuration']['paging']['page-size'];
            $searchQuery .= ' LIMIT ' . $pageStart . ',' . ($pageStart + $data['configuration']['paging']['page-size']);
        } else {
            $searchQuery .= ' LIMIT ' . $data['configuration']['paging']['page-size'];
        }
        
               
        
	// Perform the ACTUAL SEARCH!  
	$searchData = e4_db_query($searchQuery);
	
	// Load up the items that we searched for, adding them to the global data if necessary.
	$return = array();
	while($searchRecord = mysql_fetch_assoc($searchData)){
            $return[$searchRecord['ID']] = e4_data_load($searchRecord['ID'],$addToData);
	}
	
        return $return;
}

/*
 * DB FUNCTIONS
 */

function e4_db_query($SQL){
	global $db;
	
	e4_trace('Running SQL');
	e4_trace($SQL);
	
	$return = mysql_query($SQL);
	
	e4_trace('SQL complete');
	
	return $return;
}

function e4_xmlify($dataArray,$elementName = 'xml'){
	/*
	 * Turn the inbound array into XML. Recursive calls are used to work our way down the data structure.
	 */
	$xml = "<$elementName>";
	foreach($dataArray as $key=>$value){
		if (is_array($value)){
			$xml .= e4_xmlify($value,$key);
		} else {
			$xml .= "<$key>" . htmlentities($value) . "</$key>";
		}
	}
	$xml .= "</$elementName>";
	
	return $xml;
}

/*
 * HELPER FUNCTIONS FOR ENGINE4.NET
 * This is where all things that all needed by all pages live.
 */

function e4_trace($message,$posterity = FALSE){
	/*
	 * I am the trace function. I record things that have happened.
	 */
	global $data;
	$data['debug']['trace'][] = date("d/m/y : H:i:s", time()) . ' : ' . $message;
        
        /*
         * If required, I keep things for posterity
         */
        if($posterity){
            e4_db_query('INSERT INTO e4_log SET Message="' . mysql_escape_string($message) . '"');
        }
}

function e4_getGoFunction($include,$type){
	/*
	 * Work out what the "go" function of the included file is.
	 */
	$include = str_ireplace('.php', '', $include);
	$include = str_ireplace('/', '_', $include);
	return 'e4_' . $type . '_' . $include . '_go';
}

function e4_findinclude($filepath){
	/*
	 * This function locates a file to be included.
	 * It looks for it first in the domain specific directory and then, after that, in the default directory
	 * @todo Extending the list of search directories will enabled plugins that do not have to go into core.
	 */
	global $data;
	$return = 'engine4.net/void.php';
	
	if (strlen($filepath) > 0){
		$searchpaths = array();
		$searchpaths[] = e4_domaindir();
		$searchpaths[] = 'engine4.net/';
				
		foreach($searchpaths as $searchpath){
			e4_trace("Looking for $searchpath$filepath");
			if(file_exists($searchpath . $filepath)){
				e4_trace("Found $searchpath$filepath");
				$return = $searchpath . $filepath;
				break;	// Stop looking as soon as we have found the file we want and drop out to the return
			}
		}
		if ($return == 'engine4.net/void.php'){
			e4_trace("No matches for $filepath, returning void.php");
		}	
	}
	// $return = '/' . $data['configuration']['basedir'] . $return;
	return $return;
}

/*
 * DOMAIN CONTROLS
 */

function e4_domain(){
	return $_SERVER['SERVER_NAME'];
}

function e4_domaindir(){
	// $return = '/' . $_SERVER['SERVER_NAME'] . '/';
	$return = $_SERVER['SERVER_NAME'] . '/';
	return $return;
}

/*
 * TEMPLATE FUNCTIONS - CAN BE USED BY ANY RENDERER
 */

function e4_prepareTemplates(){
	/*
	 * Scan through the various templates that have been offered and decide which to use in rendering.
	 */
	global $data;
	
	$templates = array();
	if(isset($data['configuration']['renderers']['html']['templates'])){
		$templates = $data['configuration']['renderers']['html']['templates'];
	} else {
		if (isset($data['configuration']['renderers']['all']['templates'])){
			$templates = $data['configuration']['renderers']['all']['templates'];
		}
	}
	
	if (sizeof($templates) == 0){
		$templates['header'] = 'header.php';
		$templates['footer'] = 'footer.php';
	}
	if (!isset($templates['header'])){
		// Ensure we **always** have a header, even if someone forgets to put one in the templates array
		$templates['header'] = 'header.php';
	}
	if (!isset($templates['footer'])){
		// Ensure we **always** have a header, even if someone forgets to put one in the templates array
		$templates['footer'] = 'footer.php';
	}
	if (sizeof($templates) == 2){
		// Ensure we **always** at least one body template.
		// This is also the mechanism for default template selection
		$templates[] = 'home.php';
	}
	
	/*
	 * Sort the templates, to allow for appending a pre-pending templates
	 */
	ksort($templates);
	// $data['configuration']['renderers']['templates'] = $templates;
	
	return $templates;
}

function e4_findtemplate($template,$useBaseDir = FALSE){
	/*
	 * We need to find a template. Ideally we have a list of skins that we can use.
	 */
	global $data;
	$return = 'engine4.net/void.php';
	if (strlen($template) > 0){
		foreach($data['configuration']['renderers']['html']['skins'] as $skin){
			$return = e4_findinclude('templates/html/' . $skin . '/' . $template);
			if ($return !== 'engine4.net/void.php'){
				break;
			}
		}	
	}
	if ($useBaseDir){
		$return = '/' . $data['configuration']['basedir'] . $return;
	}
	return $return;
}

function e4_pickContentTemplate($content){
	/*
	 * Look at the piece of content array, and maybe the query parameters, and select an appropriate template for this piece of content
	 */
	if(is_array($content) && isset($content['type'])){
		$template = e4_findtemplate('data-types/' . strtolower($content['type']) . '.php');
		if ($template !== 'engine4.net/void.php'){
			$template = explode('/',$template);
			$templateReturn = array_pop($template);
			$templateReturn = array_pop($template) . '/' . $templateReturn;
			$template = $templateReturn;
		}
	}
	if ($template == 'engine4.net/void.php'){
		$template = 'content.php';
	}
	return $template;
	// return $content['type'] . '.php';	
}

/*
 * REDIRECTION AND ERROR HANDLING
 */

function e4_message($message,$messagetype = 'Info'){
	global $data;
	if (!isset($data['page']['messages'])){ $data['page']['messages'] = array();}
	$data['page']['messages'][] = array('message' => $message, 'type' => $messagetype); 
}

function e4_goto($redirectPath,$redirectMethod = 301){
	/*
	 * Call this function to tell engine4 to redirect you somewhere
	 */
	global $data;
	// Store the redirect ready for it be picked up at the end of the main process
	$data['redirect'] = array($redirectMethod => $redirectPath);
	// Clear the actions, as we are not going to be doing anything more/else
	// @todo Is this always the case? What about logging, and that sort of thing? Does this all have to happen *before* view?
	$data['actions'] = array();
	// Clear the renderers, as we are not going to be displaying anything
	$data['renderers'] = array();
}

function e4_redirect(){
	global $data;
	$return = FALSE;
	
	if(isset($data['redirect'])){
		$return = TRUE;
		foreach($data['redirect'] as $method=>$location){
			switch($method){
				case 301:
					header('HTTP/1.1 301 Moved Permanently');
					break;
				case 404:
					header('HTTP/1.1 404 Not Found');
					break;
				default:
			}
			header('Location: '. $location);
		}
	}
	
	return $return;
}

?>