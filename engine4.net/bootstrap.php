<?php

/* 
 * I am the engine4 boot strap file
 * I create the globals, mostly importantly the global $data array into which all things are placed.
 * I set all the default configuration parameters that are required by all pages (although these can be overridden by individual sites)
 * I control the horizontal, and the vertical 
 */

/*
 * The global data array holds EVERYTHING. 
 * This way, we always know what we have, and all actions are operate on a single set of data before passing it off to the view/renderer 
 */
$data = array();

/*
 * Configuration lives in an array
 */
$data['configuration'] = array();

$data['configuration']['basedir'] = 'github/engine4/';

$data['configuration']['database'] = array();
$data['configuration']['database']['server'] = 'localhost';
$data['configuration']['database']['username'] = 'root';
$data['configuration']['database']['password'] = '';
$data['configuration']['database']['schema'] = 'engine4';

$data['configuration']['debug'] = TRUE;

$data['configuration']['actions'] = array();
$data['configuration']['renderers'] = array();

$data['configuration']['seo'] = array();
$data['configuration']['seo']['keywords'] = array();
$data['configuration']['seo']['keywords']['count'] = 8;

$data['configuration']['paging']['page-size'] = 10;

$data['configuration']['renderers']['html']['skins'] = array('default');

/*
 * Action specific configurations
 */
$data['configuration']['checkout']['require-login'] = TRUE;

/* 
 * All data lives in this array as well
 * Data is populated by actions as the system runs
 * We need to instantiate the empty elements
 * And prepopulate any defaults
 */

/*
 * What actions are we running?
 */
$data['actions'] = array();
$data['actions'][] = 'blog/blog.php';
$data['actions'][] = 'cart/cart.php';
$data['actions'][] = 'folder/folder.php';

/*
 * What renders are we rendering?
 */
$data['renderers'] = array();
$data['renderers'][] = 'html/html.php';

/*
 * Debugging stack to keep track
 */
$data['debug'] = array();
$data['debug']['trace'] = array();

/*
 * The all important content of our page!
 */
$data['page'] = array();
$data['page']['head'] = array();
$data['page']['head']['title'] = 'engine4 Content Management';
$data['page']['head']['keywords'] = '';
$data['page']['head']['abstract'] = '';
$data['page']['head']['description'] = '';
$data['page']['head']['copyright'] = '';

$data['page']['head']['javascript'] = array();
$data['page']['head']['stylesheets'] = array();
$data['page']['body'] = array();
$data['page']['body']['H1'] = 'engine4 Content Management System';
$data['page']['body']['content'] = array();
$data['page']['body']['contentByType'] = array();
$data['page']['body']['contentByFolder'] = array();#

$page['page']['body']['sociallinks']['facebook'] = '';
$page['page']['body']['sociallinks']['twitter'] = '';

/*
 * Definitions of data types
 */

$data['configuration']['datatypes']['Blog']     = array('content'=>TRUE,'seo'=>TRUE,'social'=>TRUE);
$data['configuration']['datatypes']['Content']  = array('content'=>TRUE,'seo'=>TRUE,'social'=>TRUE);
$data['configuration']['datatypes']['Image']    = array('content'=>TRUE,'seo'=>TRUE,'social'=>TRUE);
$data['configuration']['datatypes']['Video']    = array('content'=>TRUE,'seo'=>TRUE,'social'=>TRUE);
$data['configuration']['datatypes']['Product']  = array('content'=>TRUE,'seo'=>TRUE,'social'=>TRUE);

/*
 * Supported 3rd party services
 */
$data['configuration']['3rdparty']['disqus']['shortname'] = '';
$data['configuration']['3rdparty']['facebook']['account'] = 'thefictionalist';
$data['configuration']['3rdparty']['twitter']['account'] = 'chrislynch_mwm';

$data['configuration']['3rdparty']['google-analytics']['account'] = '';
?>
