<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */

// In the meantime, just output what we have

// Decide what template we are using

if (isset($data['template'])){ $template = $data['template'];} else { $template = 'index';}

$template = file_get_contents('engine4.net/templates/' . $template . '.html');

// Find any inner templates and recursively load them up.
while (strstr($template,'<e4.template')){
	$templatecommandstart = stripos($template, '<e4.template');
	$templatecommandend = stripos($template, '/>',$templatecommandstart);
	$templatecommandlength = ($templatecommandend + 2) - $templatecommandstart;
	$templatecommand = substr($template,$templatecommandstart,$templatecommandlength);
	
	$innertemplate = str_ireplace('<e4.template.', '', $templatecommand);
	$innertemplate = str_ireplace('/>', '', $innertemplate);
	$innertemplate = trim($innertemplate);
	
	$innertemplate = file_get_contents('engine4.net/templates/' . $innertemplate . '.html');
	$template = str_ireplace($templatecommand, $innertemplate, $template);
}

// Now run any actions that we find.
// TODO: Write code to find and run actions

// Now work through the data and write it out.
foreach($data as $datakey=>$datavalue){
	if (is_array($datavalue)){
		// TODO: Go through the loop and output the repeating elements
	} else {
		// Single element, simple to output
		$template = str_ireplace("<e4.data.$datakey/>", $datavalue, $template);
		$template = str_ireplace("<e4.data.$datakey />", $datavalue, $template); // Support for slightly different formatting of commands
	}
}

// Allow for Markdown inside templates as well
$template = Markdown($template);
print $template;

?>