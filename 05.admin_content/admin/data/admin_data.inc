<?php

$datarecord = $this->_cms->loadfromdata($_GET['ID']);



foreach($datarecord as $field => $value){
	print "<h2>$field</h2>";
	print "<pre>" . $value . "</pre>";
}

?>