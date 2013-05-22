<?php

include ('_e/e.php');
$e = new e();
$e->_loadplugin('indexer');
$e->_loadplugin('config');
$e->_open('0.bootstrap');
$e->_indexer->indexfs();

?>
