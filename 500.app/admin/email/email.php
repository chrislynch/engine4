<?php

print "Opening connection\n";
$conn = imap_open('{mail.planetofthepenguins.com/notls}', 'blog456@planetofthepenguins.com', 'blog456');
print "Connection open\n";

print "Enumerating messages\n";
$msgcount = imap_num_msg($conn);
print "Found $msgcount messages\n";

// for ($i=1; $i <= $msgcount; $i++) { 
// Only process one message at a time, so that we can spread out content easily.
for ($i=1; $i <= min($msgcount,1); $i++) { 
	// Load header
	$header = imap_headerinfo($conn, $i);
	$subject = $header->subject;
	// Load body
	$body = imap_body($conn, $i);
	$txtend = stripos($body, 'Content-Type: text/html;');
	$body = substr($body, 0,$txtend);
	$body = explode("\n",$body);
	array_pop($body);
	array_pop($body);
	array_shift($body);
	array_shift($body);
	$body = implode("\n",$body);
	// Save attachments
	$attachments = array();
	$structure = imap_fetchstructure($conn, $i);
	for($a = 0; $a < count(@$structure->parts); $a++){
		if($structure->parts[$a]->disposition == 'attachment'){
			print "<pre>" . print_r($structure->parts[$a],TRUE) . "</pre>";	
			$attachment = array();
			$attachment['filename'] = '_custom/_default/content/uploads/' . $structure->parts[$a]->parameters[0]->value;
			$attachment['data'] = imap_fetchbody($conn, $i, $a+1);
            if($structure->parts[$a]->encoding == 3) { // 3 = BASE64
               $attachment['data'] = base64_decode($attachment['data']);
             }
            elseif($structure->parts[$a]->encoding == 4) { // 4 = QUOTED-PRINTABLE
               $attachment['data'] = quoted_printable_decode($attachment['data']);
            }
            $attachments[] = $attachment;
            print "Saving {$attachment['filename']}";
            file_put_contents($attachment['filename'],$attachment['data']);
		}
	}
	print "Saving message $i\n";
	email2thing($header,$body,$attachments);
	print "Deleting message $i\n";
	// imap_delete($conn,$i);
}
imap_expunge($conn);

print "Closing connection\n";
imap_close($conn);
print "Closed\n";

exit();

function email2thing($header,$body,$files=array()){
	global $e;

	$post = _cms::newThing();	

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
	$body = explode("\n",$thing->html);
	print "<pre>" . print_r($body,TRUE) . "</pre>";
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
		if (strlen(trim($thing->html)) == 0){
			// Single line posts are treated as a status
			$post['Type'] = 'status';
		} else {
			// Multi-line posts that do not start with a http:// resource are a post
			$post['Type'] = 'post';
		}
	}
	print "<pre>" . print_r($body,TRUE) . "</pre>"; 
	// Rebuild the body
	$body = implode("\n",$body);
	// Set the body using the remaining data
	$thing->html = $body;
	$post['HTML'] = $thing->html;

	// Attach any images
	if(isset($files[0])){
		$post['FeaturedImage'] = $files[0]['filename'];
	}

	print "<pre>" . print_r($post,TRUE) . "</pre>"; 

	// Save the thing
	return (_cms::saveThing($post,$files));

}

?>
