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
 * At the end of everything, we always run the "view" action.
 */

e4_action_search();

foreach($data['actions'] as $action){
	include e4_findinclude('actions/' . $action);
}

e4_prepareTemplates();

foreach($data['renderers'] as $renderer){
	include e4_findinclude('renderers/' . $renderer);
}

/*
 * Time to shut down now
 * Close the database connection
 */
mysql_close($db);

/*
 * The result of all our processing *could* be a redirect.
 * Run the redirection function in case this is the case
 */
e4_redirect();

/*
 * Output debugging information
 */
if (isset($_REQUEST['debug']) || $data['configuration']['debug']){
	print '<hr><pre>' . htmlentities(print_r($data,TRUE)) . '</pre>';
	print '<hr><pre>' . htmlentities(print_r($_REQUEST,TRUE)) . '</pre>';	
}

// 

/*
 * CONTENT DATA FUNCTIONS
 * These functions load up content and data for the system to use.
 * Lots of actions have cause to do this, so the functions live here for all to share. 
 */

function e4_data_load($ID,$addToData = TRUE){
	/*
	 * Perform a simple load of a single datacontent ID
	 */
	global $db;
	global $data;
	
	$newdata = array();
	$dataquery = e4_db_query("SELECT ID,Name,Type,URL,Timestamp,Data FROM e4_data WHERE ID = $ID");
	
	while($datarecord = mysql_fetch_assoc($dataquery)){
		// These are the header items. Make sure we have these and that they have the right name & case.
		$newdata['ID'] = $datarecord['ID'];
		$newdata['name'] = $datarecord['Name'];
		$newdata['type'] = $datarecord['Type'];
		$newdata['url'] = $datarecord['URL'];
		$newdata['timestamp'] = $datarecord['Timestamp'];
		// Everything is saved in [data]. Jump down in [data][data] to get what we need, if it exists somehow.  
		$newdata['data'] = unserialize(base64_decode($datarecord['Data']));
		if(isset($newdata['data']['data'])){
			$newdata['data'] = $newdata['data']['data'];
		}
		// Unlikely that we will need XML, so have removed it from the query above and in the line below.
		// $data['page']['body']['content'][$datarecord['ID']]['xml'] = $datarecord['XML'];
		
		if ($addToData){
			$data['page']['body']['content'][$datarecord['ID']] = $newdata;
		}
	}
	
	return $newdata;
}

function e4_data_save($saveData){
	/*
	 * Save a piece of data back to the DB
	 */
	global $db;
	
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
							 Data = "' . mysql_escape_string($serialisedSaveData) . '",
							 XML  = "' . $xmlData . '"
					  ON DUPLICATE KEY UPDATE 
					  		 Name = "' . $saveData['name'] . '",
							 Type = "' . $saveData['type'] . '",
							 URL = "' . $saveData['url'] . '",
							 Data = "' . mysql_escape_string($serialisedSaveData) . '",
							 XML  = "' . $xmlData . '"';
		// Run the query through our traced query function
		e4_db_query($saveQuery); 
		// If this was an insert using the next available ID, return that ID rather than the ID given
		if ($saveID == 0){ $saveID = mysql_insert_id($db);}
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
		$findURLQuery = e4_db_query('SELECT ID FROM e4_data WHERE type="Action" AND URL = "' . $_REQUEST['e4_url'] . '"');
		if (mysql_num_rows($findURLQuery) == 1){
			$newaction = e4_data_load(mysql_result($findURLQuery, 0),FALSE);
			$_REQUEST['e4_action'] = $newaction['data']['e4']['action'];
			$_REQUEST['e4_op'] = $newaction['data']['e4']['op'];
		}
	}
	if(isset($_REQUEST['e4_action'])){
		array_unshift($data['actions'],$_REQUEST['e4_action'] . '/' . $_REQUEST['e4_action'] . '.php');
	}
}

function e4_data_search($criteria){
	/*
	 * Perform a search on teh e4_data.
	 * TODO: This might be the first function that is going to get long enough to be unweildy in this file - think on!
	 */
	global $db;
	$searchQuery = '';
	
	/*
	 * Start with a URL lookup, if a URL has been specified
	 */
	if (isset($_REQUEST['e4_url']) AND !(isset($_REQUEST['e4_ID']))){
		$findURLQuery = e4_db_query('SELECT ID FROM e4_data WHERE  URL = "' . $_REQUEST['e4_url'] . '"');
		if (mysql_num_rows($findURLQuery) == 1){
			$_REQUEST['e4_ID'] = mysql_result($findURLQuery, 0);
		} else {
			$_REQUEST['e4_ID'] = -1;
		}
	}
	
	if(isset($_REQUEST['e4_ID'])){
		// Find an item based on its ID
		$searchQuery = 'SELECT ID FROM e4_data';
		if(isset($_REQUEST['e4_ID'])){
			$searchQuery .= ' WHERE ID =' . $_REQUEST['e4_ID'];
		}
	} else {
		if(isset($_REQUEST['e4_search'])){
			// Perform a search of some type
			$searchQuery = 'SELECT ID, MATCH(XML) AGAINST ("' . mysql_escape_string($_REQUEST['e4_search']) . '" IN BOOLEAN MODE) AS score FROM e4_data';
			$searchQuery .= ' HAVING score > 0';
		} else {
			// Generic search that just goes looking for anything
			$searchQuery = 'SELECT ID FROM e4_data';
		}	
	}

	// Perform the search and 
	$searchData = e4_db_query($searchQuery);
	while($searchRecord = mysql_fetch_assoc($searchData)){
		e4_data_load($searchRecord['ID']);
	}
	
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

function e4_trace($message){
	/*
	 * I am the trace function. I record things that have happened.
	 */
	global $data;
	$data['debug']['trace'][] = date("d/m/y : H:i:s", time()) . ' : ' . $message;
}

function e4_findinclude($filepath){
	/*
	 * This function locates a file to be included.
	 * It looks for it first in the domain specific directory and then, after that, in the default directory
	 * TODO: Extending the list of search directories will enabled plugins that do not have to go into core.
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
		$templates['head'] = 'head.php';
	}
	if (!isset($templates['head'])){
		// Ensure we **always** have a header, even if someone forgets to put one in the templates array
		$templates['head'] = 'head.php';
	}
	if (sizeof($templates) == 1){
		// Ensure we **always** at least one body template.
		// This is also the mechanism for default template selection
		$templates[] = 'home.php';
	}
	
	/*
	 * Sort the templates, to allow for appending a pre-pending templates
	 */
	ksort($templates);
	$data['configuration']['renderers']['templates'] = $templates;
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

function e4_pickContentTemplate(){
	/*
	 * Look at the data array, and the query parameters, and select an appropriate template.
	 */
	global $data;
	if (isset($_REQUEST['e4_ID']) && $_REQUEST['e4_ID'] > 0){
		return 'content.php';
	} else {
		switch (sizeof($data['page']['body']['content'])){
			case 0: return '404.php'; 		break;
			case 1: return 'content.php';   break;
			default: 
				if (isset($_REQUEST['e4_search'])){
					return 'search.php';
				} else {
					return 'home.php';
				}
		}
	}
}

/*
 * REDIRECTION AND ERROR HANDLING
 */

function e4_message($message,$messagetype = 'info'){
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
	// TODO: Is this always the case? What about logging, and that sort of thing? Does this all have to happen *before* view?
	$data['actions'] = array();
	// Clear the renderers, as we are not going to be displaying anything
	$data['renderers'] = array();
}

function e4_redirect(){
	global $data;
	if(isset($data['redirect'])){
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
}

?>