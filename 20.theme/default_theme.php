<?php

require_once '_e/lib/twig/lib/Twig/Autoloader.php';
Twig_Autoloader::register();

$loader = new Twig_Loader_Filesystem('_custom/_default/content/templates');
// $twig = new Twig_Environment($loader, array('cache' => '_custom/_default/content/cache/templates',));
$twig = new Twig_Environment($loader, array());

$template = $twig->loadTemplate('index.html');
$templatevars = get_object_vars($this);
$templatethis = array();
foreach($templatevars as $var => $value){
	if(!(substr($var,0,1) == "_")){
		$templatethis[$var] = $value;
	}
}

print $template->render(array('this' => $templatethis));

print "<pre>" . print_r($templatethis,TRUE) . "</pre>";

?>



	
	




