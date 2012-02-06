<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">

<!-- Title and SEO information -->
<title><e4.data.title/></title>
<meta name="abstract" content="<e4.data.abstract/>" />
<meta name="keywords" content="<e4.data.keywords/>" />
<meta name="description" content="<e4.data.description/>" />
<meta name="copyright" CONTENT="<e4.data.copyright/>" />
<!-- Google, Yahoo, and Bing tracking -->
<meta name="google-site-verification" content="" />
<META name="y_key" content="" />
<meta name="msvalidate.01" content="" />
<!-- URL canonicalisation -->
<link rel="canonical" href="" />
<!-- ROBOTS directives -->
<meta name="robots" content="index,follow" />

<!-- Blueprint CSS http://www.blueprintcss.org -->
<link rel="stylesheet" href="engine4.net/lib/blueprint/screen.css" type="text/css" media="screen, projection">
<!--[if lt IE 8]>
	<link rel="stylesheet" href="engine4.net/lib/blueprint/ie.css" type="text/css" media="screen, projection">
<![endif]-->

<!-- Page or Host specific CSS -->
<?php 
	if (isset($data['page']['head']['stylesheet'])){
		foreach($data['page']['head']['stylesheet'] as $css){
			print '<link rel="stylesheet" src="' . $css . '"></script>';
		}
	}
?>

<!-- CDN Hosted JQuery -->
<script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js"></script>
<script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.16/jquery-ui.min.js"></script>


<!-- Page or Host specific Javascript libraries -->
<?php 
	if (isset($data['page']['head']['javascript'])){
		foreach($data['page']['head']['javascript'] as $jquery){
			print '<script type="text/javascript" src="' . $jquery . '"></script>';
		}
	}
?>
<?php 
	if (isset($data['page']['head']['scripting'])){
		foreach($data['page']['head']['scripting'] as $script){
			print '<script type="text/javascript">' . $script . '</script>';
		}
	}
?>

<script>
$(function() {
	$( "#accordion" ).accordion({
		autoHeight: false,
		navigation: true
	});
});
</script>

<script type="text/javascript">
	var _gaq = _gaq || [];
	_gaq.push(['_setAccount', '']);
	_gaq.push(['_trackPageview']);
	(function() {
		var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
	})();
</script>