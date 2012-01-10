<?php

/*
 * Default viewing mechanism
 * This will need, somehow, to support some templates.
 * I've created a "templates" directory, just need to work out how this maps to the views, etc.
 */

// In the meantime, just output what we have

// Decide what template we are using

if (isset($data['template'])){ $template = $data['template'];} else { $template = 'view';}
if (isset($data['enclosure'])){ $enclosure = $data['enclosure'];} else { $enclosure = 'index';}

$output = file_get_contents(e4_find('templates',$enclosure));

// Find any inner templates and recursively load them up.
while (strstr($output,'<e4.template')){
	$templatecommandstart = stripos($output, '<e4.template');
	$templatecommandend = stripos($output, '/>',$templatecommandstart);
	$templatecommandlength = ($templatecommandend + 2) - $templatecommandstart;
	$templatecommand = substr($output,$templatecommandstart,$templatecommandlength);
	
	$innertemplate = str_ireplace('<e4.template.', '', $templatecommand);
	$innertemplate = str_ireplace('/>', '', $innertemplate);
	$innertemplate = trim($innertemplate);
	if (strlen($innertemplate) == 0) { $innertemplate = $template; }
	
	$innertemplate = file_get_contents(e4_find('templates',$innertemplate));
	$output = str_ireplace($templatecommand, $innertemplate, $output);
}

// Now run any actions that we find.
// TODO: Write code to find and run actions

// Now work through the data and write it out.
foreach($data as $datakey=>$datavalue){
	if (is_array($datavalue)){
		$repeatStart = stripos($output, "<e4.data.$datakey>");
		$repeatTemplateStart = $repeatStart + strlen("<e4.data.$datakey>");
		$repeatEnd = stripos($output,"</e4.data.$datakey>") - 1;
		$outputTemplate = substr($output, $repeatTemplateStart,$repeatEnd - $repeatTemplateStart);
		print $outputTemplate;
		$repeaterOutput = '';
		foreach($datavalue as $idatakey=>$idatavalue){
			 $repeaterOutput .= $outputTemplate;
			 foreach($idatavalue as $iidatakey => $iidatavalue){
			 	$repeaterOutput = str_ireplace("<e4.data.$iidatakey/>", $iidatavalue, $repeaterOutput);
				$repeaterOutput = str_ireplace("<e4.data.$iidatakey />", $iidatavalue, $repeaterOutput); // Support for slightly different formatting of commands
			 }
		}
		$output = substr($output,0,$repeatStart) . $repeaterOutput . substr($output,$repeatEnd);
	} else {
		// Single element, simple to output
		$output = str_ireplace("<e4.data.$datakey/>", $datavalue, $output);
		$output = str_ireplace("<e4.data.$datakey />", $datavalue, $output); // Support for slightly different formatting of commands
	}
}

// Allow for Markdown inside templates as well
// $output = Markdown($output);
print $output;

?>