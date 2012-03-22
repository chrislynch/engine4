<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
		
		<!-- Title and SEO information -->
		<title><?=@$data['page']['head']['title']?></title>
		
		<meta name="abstract" content="<?=@$data['page']['head']['abstract']?>" />
		<meta name="keywords" content="<?=@$data['page']['head']['keywords']?>" />
		<meta name="description" content="<?=@$data['page']['head']['description']?>" />
		<meta name="copyright" CONTENT="<?=@$data['page']['head']['copyright']?>" />
		
		<!-- Google, Yahoo, and Bing tracking -->
		<meta name="google-site-verification" content="" />
		<META name="y_key" content="" />
		<meta name="msvalidate.01" content="" />
		
		<!-- URL canonicalisation -->
		<link rel="canonical" href="" />
		
		<!-- ROBOTS directives -->
		<meta name="robots" content="index,follow" />
		
		<!-- Blueprint CSS http://www.blueprintcss.org -->
		<link rel="stylesheet" href="@@configuration.basedir@@engine4.net/lib/blueprint/src/grid.css" type="text/css" media="screen, projection">		
		<!--[if lt IE 8]>
			<link rel="stylesheet" href="engine4.net/lib/blueprint/ie.css" type="text/css" media="screen, projection">
		<![endif]-->
                <!-- Droid Sans Font -->
                <link href='http://fonts.googleapis.com/css?family=Droid+Sans' rel='stylesheet' type='text/css'>
		
		<!-- Page or Host specific CSS -->
		<?php
                    print '<link rel="stylesheet" href="@@configuration.basedir@@' . e4_findtemplate('style.css') . '" type="text/css" media="screen, projection">';
                    if (isset($data['page']['head']['stylesheet'])){
                        foreach($data['page']['head']['stylesheet'] as $css){
                            print '<link rel="stylesheet" href="@@configuration.basedir@@' . $css . '" type="text/css" media="screen, projection">';
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
					print '<script type="text/javascript" src="@@configuration.basedir@@' . $jquery . '"></script>';
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
	</head>
        <body>