<?php
/*
 * Handler for all single page webforms, such as a contact us form
 */

if(isset($_REQUEST['e4_webform_op'])){
	/*
	 * A form, in some way, has been submitted. Go and find out what was done!
	 * Send an email about it.
	 */
	$message = '';
	foreach($_REQUEST as $key=>$value){
		if (strstr($key,'e4_webform') && $key !== 'e4_webform_op'){
			$message .= $key . ': ' . $value . chr(10) . chr(13);
		}
	}
	mail('github@planetofthepenguins.com', 'Form submission', $message);
}

if(!isset($_REQUEST['e4_webform_op'])){
	if (isset($_REQUEST['e4_op']) && strlen($_REQUEST['e4_op']) > 0){
		// If an operation is passed, use this as our form template.
		$data['configuration']['renderers']['all']['templates'][0] = $_REQUEST['e4_op'];
	} else {
		// If an operation is not passed, display a default contact form.
		$data['configuration']['renderers']['all']['templates'][0] = 'contact.php';
	}	
} else {
	e4_message('Thank you! We will respond ASAP.');
}
 