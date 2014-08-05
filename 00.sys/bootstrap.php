<?php
$this->_loadPlugin('config');
$this->_loadPlugin('db');
$this->_loadPlugin('cms');
$this->_loadPlugin('messaging');

$this->_config->set('smtp.host','');
$this->_config->set('smtp.user','');
$this->_config->set('smtp.password','');

$this->_config->set('mysql.server','127.0.0.1');
$this->_config->set('mysql.database','fictional_e');
$this->_config->set('mysql.user','root');
$this->_config->set('mysql.password','');

?>