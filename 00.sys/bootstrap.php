<?php

// TODO: Work out which directory we are running from 

// Connect to the SQLite database in the running directory
// TODO: (This may, in turn, contain MySQL database connection details to override DB, below)
// $this->_db = new PDO("sqlite:_custom/_default/data/f.db");
$this->_loadPlugin('db');
$this->_loadPlugin('cms');
// 

$this->sys->custompath = '_custom/_default/';

?>
