<?php
/*
 * This is the primary index file for an e4 installation.
 * It works out what action and view we are using, calls that action, then passes the resulting data over to the view to be rendered.
 */

include 'helper.php';

/*
 * Start by grabbing and breaking down the URL
 * Also, set the default action and default view, in case neither can be found.
 */

$url = parse_url($_SERVER['REQUEST_URI']);
if (isset($_REQUEST['e4'])){ $url['path'] = $_REQUEST['e4'];}
$action = 'engine4.net/actions/view.php';
$view = 'engine4.net/views/view.php';

/*
 * Now break down the URL, looking for actions, views, and content
 */
$path = $url['path'];
$path = explode('/',$path);
while (strlen($path[0]) == 0){ array_shift($path); }


// ACTIONS
// $searchpath = 'engine4.net/actions/' . str_ireplace('_', '/', $path[0]) . '.php';
$searchpath = e4_find('actions',$path[0]);
if (file_exists($searchpath)){
	// We have found an action!
	// Store the action and pop this piece off the front of the path array
	$action = $searchpath;
	array_shift($path);
}

// VIEWS
if (isset($path[0])){
	// $searchpath = 'engine4.net/views/' . str_ireplace('_', '/', $path[0]) . '.php';
	$searchpath = e4_find('views',$path[0]);
	if (is_dir($searchpath)){
		// We have found an action!
		// Store the action and pop this piece off the front of the path array
		$view = $searchpath;
		array_shift($path);
	}	
}

// CONTENT
// Whatever remains, after we have found actions and views, is the content
$content = implode('/',$path);
$content = str_ireplace('github/engine4/', '', $content);

if (isset($_REQUEST['debug'])){
print "
<pre>
URL = " . print_r($url,TRUE) . "
Path = " . print_r($path,TRUE) . "
Action = $action
View = $view
Content = $content
</pre>";	
}

// So, we have now identified our action and our view.
// Include the action, which should set up the $data array, then call the view, which should render it
$data = array();

include $action;

if (isset($_REQUEST['debug'])){
print "
<pre>
Data = " . print_r($data,TRUE) . "
</pre>";	
}

include $view;

?>