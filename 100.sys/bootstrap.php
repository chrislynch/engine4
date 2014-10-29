<?php

// TODO: Work out which directory we are running from 

// Connect to the SQLite database in the running directory
// TODO: (This may, in turn, contain MySQL database connection details to override DB, below)
// $this->_db = new PDO("sqlite:_custom/_default/data/f.db");
$this->_loadPlugin('db');
$this->_loadPlugin('cms');
// 

$this->sys->custompath = '_custom/_default/';
$this->sys->basehref = $this->_basehref();

if($this->parray(0) == 'admin' && $this->p !== 'admin/user' && $this->p !== 'admin/email'){
	// We are on the /admin path. Need an admin user
	if(@$_COOKIE['_e_admin'] !== '1'){
		$this->_goto('admin/user');
	}
}

// Load up the configuration
$this->sys->config = _cms::loadID(-1);

?>
