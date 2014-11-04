<?php

print "Opening connection\n";
$conn = imap_open('{mail.planetofthepenguins.com/notls}', 'blog123@planetofthepenguins.com', 'blog123');
print "Connection open\n";

print "Enumerating messages\n";
$msgcount = imap_num_msg($conn);
print "Found $msgcount messages\n";

for ($i=1; $i <= $msgcount; $i++) { 
	$header = imap_headerinfo($conn, $i);
	$subject = $header->subject;
	$body = imap_body($conn, $i);
	$txtend = stripos($body, 'Content-Type: text/html;');
	$body = substr($body, 0,$txtend);
	$body = explode("\n",$body);
	array_pop($body);
	array_pop($body);
	array_shift($body);
	array_shift($body);
	$body = implode("\n",$body);
	print "Saving message $i\n";
	email2thing($header,$body);
	print "Deleting message $i\n";
	imap_delete($conn,$i);
}
imap_expunge($conn);

print "Closing connection\n";
imap_close($conn);
print "Closed\n";

exit();

function email2thing($header,$body,$files=array()){
	global $e;

	$post = _cms::newThing();	
	$files = array();

	// Default to creating a new item
	$post['ID'] = 0;
	// Assume the item will be published
	$post['Status'] = 1;
	// Assume the item belongs on the front page
	$post['System'] = 1;

	// Read the header and pick up any useful info
	$post['Name'] = $header->subject;

	// Create a standard thing and use it to extract variables
	$thing = new eThing;
	$thing->html = $body;
	$thing->extractVariables();
	
	$data = get_object_vars($thing);
	foreach($data as $key=>$value){
		$post[$key] = $value;
	}

	// Parse the remaining text and work out what we are doing based on message content
	$body = explode("\n",$body);
	$line1 = $body[0];
	if (stripos($line1, 'http') === 0){
		// If the line starts with http:// we could be looking at either a video or a link.
		if (stripos($line1, 'youtube')){
			// This is a video
			$post['Type'] = 'video';
			$post['Video'] = $line1;
			unset($body[0]);
		} else {
			// This is a link (for now, this is treated as a post)
			$post['Type'] = 'link';
			$post['Link'] = $line1;
			unset($body[0]);
		}
	} else {
		// This is a post or a status
		if (count($body) == 0){
			// Single line posts are treated as a status
			$post['Type'] = 'status';
		} else {
			// Multi-line posts that do not start with a http:// resource are a post
			$post['Type'] = 'post';
		}
	}
	// Rebuild the body
	$body = implode("\n",$body);

	// Set the body using the remaining data
	unset($post['html']);
	$post['HTML'] = $thing->html;

	print_r($post);

	// Save the thing
	return (_cms::saveThing($post,$files));

}

?>
