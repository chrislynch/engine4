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
$data['configuration']['database'] = array();
$data['configuration']['database']['server'] = 'localhost';
$data['configuration']['database']['username'] = 'root';
$data['configuration']['database']['password'] = '';
$data['configuration']['database']['schema'] = 'engine4';

$data['configuration']['debug'] = TRUE;

$data['configuration']['actions'] = array();
$data['configuration']['renderers'] = array();

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
$data['actions'][] = 'view/view.php';

/*
 * What views are we rendering?
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
$data['page']['head']['javascript'] = array();
$data['page']['head']['stylesheets'] = array();
$data['page']['body'] = array();
$data['page']['body']['H1'] = 'engine4 Content Management System';
$data['page']['body']['content'] = array();
?>