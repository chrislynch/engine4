<?php

print "Opening connection\n";
$conn = imap_open('{mail.planetofthepenguins.com/notls}', 'blog123@planetofthepenguins.com', '');
print "Connection open\n";

print "Enumerating messages\n";
$msgcount = imap_num_msg($conn);
print "Found $msgcount messages\n";

for ($i=1; $i <= $msgcount; $i++) { 
	print "Printing message $i\n\t";
	$header = imap_headerinfo($conn, $i);
	$subject = $header['subject'];
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
}

print "Closing connection\n";
imap_close($conn);
print "Closed\n";

function email2thing($header,$body,$files=array()){
	$subject = $header['subject'];
}

?>
