<?php
$data['configuration']['database']['schema'] = 'potp';
$data['configuration']['renderers']['html']['skins'] = array('ageofsteam','default');
$data['configuration']['renderers']['all']['templates'][-1] = 'body-start.php';
$data['configuration']['renderers']['all']['templates'][9] = 'body-end.php';
$data['configuration']['install'] = TRUE;
?>
