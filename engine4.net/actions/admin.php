<?php
/*
 * This is the admin file.
 * This is where our CMS lives, basically.
 */

$data['template'] = 'admin/home';

if (isset($_REQUEST['admin_operation'])){
	switch ($_REQUEST['admin_operation']){
		case 'Save':
			print 'Saving ' . $_REQUEST['content_filename'];
			break;
		default:
	}
}

?>