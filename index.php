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

e4_data_save(array('ID'=>1,'Title'=>'Hello','Body'=>'Hello there. How are you?'));
e4_data_save(array('ID'=>2,'Title'=>'Goodbye','Body'=>'OK, Bye-Bye then'));

if(isset($_REQUEST['e4_action'])){
	array_unshift($data['actions'],$_REQUEST['e4_action'] . '/' . $_REQUEST['e4_action'] . '.php');
}

foreach($data['actions'] as $action){
	include e4_findinclude('actions/' . $action);
}

foreach($data['renderers'] as $action){
	include e4_findinclude('renderers/' . $action);
}

/*
 * Time to shut down now
 * Close the database connection
 */
mysql_close($db);

/*
 * Output debugging information
 */
if (isset($_REQUEST['debug']) || $data['configuration']['debug']){
	print '<pre>' . print_r($data,TRUE) . '</pre>';	
}

// 

/*
 * CONTENT DATA FUNCTIONS
 * These functions load up content and data for the system to use.
 * Lots of actions have cause to do this, so the functions live here for all to share. 
 */

function e4_data_load($ID){
	/*
	 * Perform a simple load of a single datacontent ID
	 */
	global $db;
	global $data;
	
	$dataquery = e4_db_query("SELECT ID,Data,XML FROM e4_data WHERE ID = $ID");
	while($datarecord = mysql_fetch_assoc($dataquery)){
		$data['page']['body']['content'][$datarecord['ID']] = array();
		$data['page']['body']['content'][$datarecord['ID']]['ID'] = $datarecord['ID'];
		$data['page']['body']['content'][$datarecord['ID']]['data'] = unserialize(base64_decode($datarecord['Data']));
		$data['page']['body']['content'][$datarecord['ID']]['xml'] = $datarecord['XML'];
	}
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
	
	$saveQuery = 'INSERT INTO e4_data 
					SET  ID = ' . $saveID . ',
						 Data = "' . mysql_escape_string($serialisedSaveData) . '",
						 XML  = "<xml></xml>"
				  ON DUPLICATE KEY UPDATE 
						 Data = "' . mysql_escape_string($serialisedSaveData) . '",
						 XML  = "<xml></xml>"';
	e4_db_query($saveQuery); 
}

function e4_data_search($criteria){
	/*
	 * Perform a search on teh e4_data.
	 * TODO: This might be the first function that is going to get long enough to be unweildy in this file - think on!
	 */
	global $db;
	
	$searchQuery = 'SELECT ID FROM e4_data';
	// TODO: This is where the criteria will go
	// TODO: This is where the limiters and paging will go
	
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
	$searchpaths = array();
	$searchpaths[] = e4_domaindir();
	$searchpaths[] = 'engine4.net/';
	$return = 'engine4.net/void.php';
	
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
	
	return $return;
}

function e4_domaindir(){
	$return = '/' . $_SERVER['SERVER_NAME'] . '/';
	return $return;
}

?>