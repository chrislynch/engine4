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
 * MESSAGE COOKIES
 * We may have some messages stashed in cookies from a previous page run.
 * Grab them and put them back in the data array for output later
 */

$data['page']['messages'] = unserialize(cookie_get('messages', serialize(array())));

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
    $data['actions-completed'] = array();
    foreach($data['actions'] as $action){
        if (!key_exists($action, $data['actions-completed'])){
            include_once e4_findinclude('actions/' . $action);
            e4_trace('Looking for function ' . e4_getGoFunction($action,'action'));
            if (function_exists(e4_getGoFunction($action,'action'))){
                    e4_trace('Found function ' . e4_getGoFunction($action,'action'));
                    $parameters = array( &$data );
                    call_user_func_array(e4_getGoFunction($action,'action'),$parameters);
            } else {
                    e4_trace('Could not find function ' . e4_getGoFunction($action,'action'));
            }
            $data['actions-completed'][$action] = $action;
        }
    }
    unset($data['actions-completed']);
}

// Record generic stats
e4_action_analytics_analytics_go($data);

/*
 * Only actions can re/write the DB. Close the connection now.
 */
mysql_close($db);

// Serialise the messages from the actions into a cookie in case the renderer does not show them immediately.
cookie_set('messages',  serialize($data['page']['messages']));

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
 * COOKIE FUNCTIONS
 */

function cookie_set($cookiename,$cookievalue){
    $cookiename = 'e4_' . e4_domain() . '_' . $cookiename;
    $cookiename = str_ireplace('.', '_', $cookiename);
    setcookie($cookiename,$cookievalue,0,'/');
    $_REQUEST['cookie_' . $cookiename] = $cookievalue; // Tuck cookie value here in case it is needed during this page render 
}

function cookie_get($cookiename,$defaultvalue = '',$widget = FALSE){
    if (!$widget) {$cookiename = 'e4_' . e4_domain() . '_' . $cookiename;}
    $cookiename = str_ireplace('.', '_', $cookiename);

    if (isset($_REQUEST['cookie_' . $cookiename])){
        return $_REQUEST['cookie_' . $cookiename];
    } else {
        if (isset($_COOKIE[$cookiename])){
                return $_COOKIE[$cookiename];
        } else {
                return $defaultvalue;	
        }
    }
}

/*
 * CONTENT DATA FUNCTIONS
 * These functions load up content and data for the system to use.
 * Lots of actions have cause to do this, so the functions live here for all to share. 
 */

function e4_data_new($type = ''){
	/*
	 * Create a new data item array and return it for use.
	 */
	$newitem = array('ID'=>0,'name'=>'','type' => '', 'url' => '', 'folder' => '', 'iscontent' => 1, 'status' => 1);
        $newitem['data'] = array();
        $newitem['linkages'] = array();
        $newitem['tags'] = array();
        
        // Check for a general content_new function that is specific to this content type
        if ($type !== ''){
            $newitem['type'] = $type;
            include_once e4_findinclude('data-types/' . $type . '.php');
            $newfunction = 'e4_datatype_' . $type . '_new';
            if (function_exists($newfunction)){
                $parameters = array( &$newitem );
                call_user_func_array($newfunction,$parameters);
            }
        }
               
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
        $linkagequery = e4_db_query('SELECT ID,LinkType,LinkID FROM e4_linkage WHERE ID = ' . $ID . ' OR LinkID = ' . $ID);
        
        while($linkagerecord = mysql_fetch_assoc($linkagequery)){
            if ($loadLinkages === TRUE){
                if ($linkagerecord['ID'] == $ID){
                    $newdata['linkages']['to'][$linkagerecord['LinkType']][] = e4_data_load($linkagerecord['LinkID'], FALSE, FALSE);
                } else {
                    $newdata['linkages']['from'][$linkagerecord['LinkType']][] = e4_data_load($linkagerecord['ID'], FALSE, FALSE);
                }
            } else {
                if ($linkagerecord['ID'] == $ID){
                    $newdata['linkages']['to'][$linkagerecord['LinkType']][] = $linkagerecord['LinkID'];
                } else {
                    $newdata['linkages']['from'][$linkagerecord['LinkType']][] = $linkagerecord['LinkID'];
                }
            }
        }
        
        $newdata['tags'] = array();
        $tagquery = e4_db_query('SELECT TagType,Tag FROM e4_tags WHERE ID = ' . $ID);
        while($tagrecord = mysql_fetch_assoc($tagquery)){
            if(!isset($newdata['tags'][$tagrecord['TagType']])){ $newdata['tags'][$tagrecord['TagType']] = array(); }
            $newdata['tags'][$tagrecord['TagType']][] = $tagrecord['Tag'];
        }
        
        // Call the load function applicable to this datatype
        if (isset($newdata['type'])){
            include_once e4_findinclude('data-types/all.php');
            include_once e4_findinclude('data-types/' . $newdata['type'] . '.php');

            e4_datatype_all_load($newdata);

            if (function_exists('e4_datatype_' . $newdata['type'] . '_load')){
                $parameters = array( &$newdata );
                call_user_func_array('e4_datatype_' . $newdata['type'] . '_load', $parameters);
            }    
        }
	
        if ($addToData){
            $data['page']['body']['content'][$newdata['ID']] = $newdata;
            $data['page']['body']['contentByType'][$newdata['type']][$newdata['ID']] = $newdata;
            $data['page']['body']['contentByFolder'][$newdata['folder']][$newdata['ID']] = $newdata;
            foreach($newdata['tags'] as $TagType => $Tag){
                if (!isset($data['page']['body']['contentByTag'][$TagType])){ $data['page']['body']['contentByTag'][$TagType] = array();}
                foreach($Tag as $TagID=>$TagName){
                    if (!isset($data['page']['body']['contentByTag'][$TagType][$TagName])) { $data['page']['body']['contentByTag'][$TagType][$TagName] = array();}
                     $data['page']['body']['contentByTag'][$TagType][$TagName][] = $newdata;
                }
                
            }
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

function e4_data_save_link($linkFrom,$linkTo,$linkType){
    e4_db_query('INSERT INTO e4_linkage SET 
                    ID = ' . $linkFrom . ', 
                    LinkType = "' . $linkType . '",
                    LinkID = ' . $linkTo);
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
                if (!isset($_REQUEST['e4_' . $_REQUEST['e4_action'] . '_op'])){ $_REQUEST['e4_' . $_REQUEST['e4_action'] . '_op'] = $newaction['data']['e4']['op']; }
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
	
        // Final check. Look at the first element of the URL and decide if this could be an action
        
        if (!isset($_REQUEST['e4_action'])){
            if (isset($_REQUEST['e4_url']) && $_REQUEST['e4_url'] !== ''){
                $actionURL = $_REQUEST['e4_url'];
                $actionURL = explode('/',$actionURL);
                $testAction = 'actions/' . $actionURL[0] . '/' . $actionURL[0] . '.php';
                $testAction = e4_findinclude($testAction);
                if ($testAction !== 'void.php'){
                    $_REQUEST['e4_action'] = $actionURL[0];
                    if (isset($actionURL[1])){
                        $_REQUEST['e4_' . $_REQUEST['e4_action'] . '_op'] = $actionURL[1];
                    }
                    unset($_REQUEST['e4_url']);              // Unset the requesting URL to ensure the we don't try to load up content from it later.
                }
            } else {
                $_REQUEST['e4_action'] = 'home';
            }
        }
        
        if(isset($_REQUEST['e4_action'])){
            if ($_REQUEST['e4_action'] !== 'security'){
                array_push($data['actions'],$_REQUEST['e4_action'] . '/' . $_REQUEST['e4_action'] . '.php');
            }
	} 
        
        // Add in VIEW as the last and final action every time.
        array_push($data['actions'],'view/view.php');
        
}

function e4_data_search($criteria=array(),$addToData = TRUE,$onlyContent=TRUE,$suppressID=FALSE){
    global $data;
	/*
	 * Perform a search on the e4_data.
	 * @todo This might be the first function that is going to get long enough to be unweildy in this file - think on!
	 */
	global $db;
	$searchQuery = '';
	
	if (isset($_REQUEST['e4_action']) && $_REQUEST['e4_action'] == 'admin'){
		$admin=TRUE;
	} else {
		$admin=FALSE;	
	}
	
	/*
	 * Start with a URL lookup, if a URL has been specified and no criteria exist
	 */
        if (sizeof($criteria) == 0){
            if (isset($_REQUEST['e4_url']) AND !(isset($_REQUEST['e4_ID']))){
		$findURLQuery = e4_db_query('SELECT ID FROM e4_data WHERE URL = "' . $_REQUEST['e4_url'] . '" AND status <> 0 AND Type<>"Action"');
		if (mysql_num_rows($findURLQuery) == 1){
			$_REQUEST['e4_ID'] = mysql_result($findURLQuery, 0);
		} else {
			$_REQUEST['e4_ID'] = -1;
		}
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
	
        if(isset($_REQUEST['e4_search'])){
            $searchQueryKeywords[] = '"' . $_REQUEST['e4_search'] . '"';
        }
        foreach($_REQUEST as $field=>$value){
            if (strstr($field,'e4_search_criteria')){
                if(!is_array($criteria)){$criteria = array();}
                $field = str_ireplace('e4_search_criteria_', '', $field);
                $criteria[$field] = $value;
            }
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
	
	if(!$suppressID && isset($_REQUEST['e4_ID'])){
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
            $data['page']['pager']['recordcount'] = $pageCount;
            if ($pageCount < $data['configuration']['paging']['page-size']) {
                $pageCount = 1;
            } else {
                $pageCount = intval($pageCount / $data['configuration']['paging']['page-size']);
                if ($pageCount % $data['configuration']['paging']['page-size'] > 0) { $pageCount += 1;}
            }
        } else {
            $pageCount = 0;
        }
        $data['page']['pager']['pagecount'] = $pageCount;
        if ($pageCount > 0){
            // TODO: Anything to do with pager?
        }
        
        // Now adding the paging to the query
        if (isset($_REQUEST['e4_page']) && is_numeric($_REQUEST['e4_page'])){
            $pageStart = $_REQUEST['e4_page'] * $data['configuration']['paging']['page-size'];
            $searchQuery .= ' LIMIT ' . $data['configuration']['paging']['page-size'] . ',' . $pageStart;
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
	$return = 'void.php';
	
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
		if ($return == 'void.php'){
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
	$return = 'void.php';
	if (strlen($template) > 0){
		foreach($data['configuration']['renderers']['html']['skins'] as $skin){
			$return = e4_findinclude('templates/html/' . $skin . '/' . $template);
			if ($return !== 'void.php'){
				break;
			}
		}	
	}
	if ($useBaseDir){
		$return = '/' . $data['configuration']['basedir'] . $return;
	}
	return $return;
}

function e4_pickContentTemplate($content,$viewtype = 'all',$component = '',$contentTypeOverride = '', $viewtypeOverride = ''){
    /*
    * Look at the piece of content, the type of view we are on, and try to find the relevant component.
    */
    
    // Work out our target file name based on the components
    $targetfilename = 'data-types/';
    
    if($contentTypeOverride !== ''){ $targetfilename .= $contentTypeOverride . '-';} 
        else { $targetfilename .= strtolower($content['type']) . '-'; }
    
    if ($viewtypeOverride !== ''){ $targetfilename .= $viewtypeOverride;}
        else {$targetfilename .= $viewtype;}
    
    if ($component !== ''){ $targetfilename .=  '-' . $component . '.php'; }
    
    // Look for the file that we want
    $template = e4_findtemplate($targetfilename);
    e4_trace('Picking template ' . $targetfilename . ' and found ' . $template);
     
    // If the file exists, return it
    if ($template == 'void.php'){
        // We did not find the file. 
        // Recurse and look for something less specific
        if ($viewtypeOverride == '' && $contentTypeOverride == ''){
            // No overrides tried yet - try a viewtype override
            return e4_pickContentTemplate($content, $viewtype, $component, '', 'all');
        } 
        if ($viewtypeOverride !== '' && $contentTypeOverride == ''){
            // Second standby, try moving to the generic "content" content type
            return e4_pickContentTemplate($content, $viewtype, $component, 'content','');
        }
        if ($viewtypeOverride == '' && $contentTypeOverride !== ''){
            // Second standby, try moving to the generic "content" content type
            return e4_pickContentTemplate($content, $viewtype, $component, 'content','all');
        }
        if ($viewtypeOverride !== '' && $contentTypeOverride !== ''){
            // We have failed to find an override that matches.
            return '';
        }
    } elseif ($template == '') {
        // We have given up looking. Return void.php
        return 'void.php';
    } else {
        // We have found the template
        $template = explode('/',$template);
        $templateReturn = array_pop($template);
        $templateReturn = array_pop($template) . '/' . $templateReturn;
        $template = $templateReturn;
        return $template;
    }
    
}

/*
 * REDIRECTION, URLs, AND ERROR HANDLING
 */

function e4_BuildURL($params = array(),$retainExisting = TRUE,$path = ''){
    /*
     * Build a new URL, retaining and overwriting OR replacing URLs as required
     */
    
    $return = '';
    $returnParams = array();
    $disposableParams = array('e4_url');
    
    if ($retainExisting){
        foreach($_GET as $getKey=>$getValue){
            if (!key_exists($getKey,$disposableParams)){
                if (key_exists($getKey, $params)){
                    if ($params[$getKey] !== ''){
                        $returnParams[] = $getKey . '=' . $params[$getKey];
                    }
                    unset($params[$getKey]);
                } else {
                    $returnParams[$getKey] = $getKey . '=' . $getValue;
                }
            }
        }
    }
        
    foreach($params as $key=>$value){
        $returnParams[] = $key . '=' . $value;
    }
    
    $return = implode('&', $returnParams);
    $return = $path . '?' . $return;
    return $return;
}

function e4_message($message,$messagetype = 'Info'){
	global $data;
	if (!isset($data['page']['messages'])){ $data['page']['messages'] = array();}
	$data['page']['messages'][] = array('message' => $message, 'type' => $messagetype);
        cookie_set('messages',  serialize($data['page']['messages']));
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