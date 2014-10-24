<?php

print "Opening connection\n";
$conn = imap_open('{mail.planetofthepenguins.com/notls}', 'blog123@planetofthepenguins.com', 'blog123');
print "Connection open\n";

print "Enumerating messages\n";
$msgcount = imap_num_msg($conn);
print "Found $msgcount messages\n";

for ($i=1; $i <= $msgcount; $i++) { 
	print "Printing message $i\n\t";
	$header = imap_headerinfo($conn, $i);
	$subject = $header->subject;
	print_r($header);
	$body = imap_body($conn, $i);
	$txtend = stripos($body, 'Content-Type: text/html;');
	$body = substr($body, 0,$txtend);
	$body = explode("\n",$body);
	array_pop($body);
	array_pop($body);
	array_shift($body);
	array_shift($body);
	$body = implode("\n",$body);
	print $body;
	print "\n";
	email2thing($header,$body);
}

print "Closing connection\n";
imap_close($conn);
print "Closed\n";

function email2thing($header,$body,$files=array()){
	$post = _cms::newThing();
	$files = array();

	// Read the header and pick up any useful info
	$post['Name'] = $header->subject;

	// Parse the body text and work out what we are doing
	$body = explode("\n",$body);
	$line1 = $body[0];
	if (stripos($line1, 'http://') === 0){
		// If the line starts with http:// we could be looking at either a video or a link.
		if (stripos($line1, 'youtube')){
			// This is a video
			$post['Type'] == 'video';
		} else {
			// This is a link (for now, this is treated as a post)
			$post['Type'] == 'post';
		}
	} else {
		// This is a post or a status
		if (count($body) == 1){
			// Single line posts are treated as a status
			$post['Type'] == 'status';
		} else {
			// Multi-line posts that do not start with a http:// resource are a post
			$post['Type'] == 'post';
		}
	}

	$body = implode("\n",$body);
	$post['HTML'] = $body;

	_cms::saveThing($post,$files);

}

?>
