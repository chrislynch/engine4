<?php
$textareatext = '';

if (isset($_POST['bubbletext'])){
    if(@$_POST['op'] == 'SAVE') {
			$title = explode("\n\r",$_POST['bubbletext']);
			$title = "bubble-" . date("Ymd") . "-" . date("Hms");
			header('Content-type: text/plain');
			header("Content-Disposition: attachment; filename=$title.txt");
			print $_POST['bubbletext'];
			die();
    } elseif(@$_POST['op'] == 'UPLOAD') {
			foreach($_FILES as $field => $file){
				if($file['error'] == 0){
					$textareatext .= file_get_contents($file['tmp_name']);
				} 
			}
    } else {
			print Bubble($_POST['bubbletext']);
			die();
    }
}

?>

<html>
<head>
	<!-- JQuery -->
	<script src="http://code.jquery.com/jquery-2.1.1.min.js"></script>
		
	<!-- Bootstrap -->
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap.min.css">
	<!-- Optional theme -->
	<link rel="stylesheet" href="//netdna.bootstrapcdn.com/bootstrap/3.2.0/css/bootstrap-theme.min.css">
	<!-- Latest compiled and minified JavaScript -->
	<script src="//netdna.bootstrapcdn.com/bootstrap/3.2.0/js/bootstrap.min.js"></script>

	<style>
		#left { height: auto; }
		#left textarea { width: 100%; height: calc(100% - 120px); }
		#right { height: auto; }
		#right #bubbleout { width: 100%; height: 90%; overflow: scroll}
	</style>

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-1031684-2', 'auto');
  ga('send', 'pageview');

</script>

</head>
<body>
<form id="bubblein" method="POST" action="index.php" enctype="multipart/form-data">
	<nav class="navbar navbar-default" role="navigation">
		<div class="container-fluid">
		  <!-- Brand and toggle get grouped for better mobile display -->
		  <div class="navbar-header">
		    <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
		      <span class="sr-only">Toggle navigation</span>
		      <span class="icon-bar"></span>
		      <span class="icon-bar"></span>
		      <span class="icon-bar"></span>
		    </button>
		    <a class="navbar-brand" href="#">Bubble: Plain Text Comic Book Scripting</a>
		  </div>

		  <!-- Collect the nav links, forms, and other content for toggling -->
		  <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
		    <ul class="nav navbar-nav">
					<li><button type="submit" name="op" class="btn btn-primary navbar-btn">Print Script</button>&nbsp;&nbsp;</li>
					<li><button type="submit" name="op" value="SAVE" class="btn btn-success navbar-btn">Save Script</button></li>
		    </ul>
				<ul class="nav navbar-nav navbar-right">
					<p class="navbar-text">
						<input name='upload' type='file'>
					</p>
					<button type="submit" name="op" class="btn btn-danger navbar-btn" value="UPLOAD">Upload TXT</button>
				</ul>
		  </div><!-- /.navbar-collapse -->
		</div><!-- /.container-fluid -->
	</nav>

	<div class="container-fluid">
		<div id="left" class="col-lg-6 col-md-8 col-sm-12 col-xs-12">			
			<textarea name="bubbletext" class="formcontrol"><?php if(strlen($textareatext) > 0) { print $textareatext; } ?></textarea>
		</div>
		<div id="right" class="col-lg-6 col-md-4 col-sm-12 col-xs-12">
			<div id="bubbleout"></div>
			<div id="counter"></div>
		</div>
	</div>
</form>

</body>

<script>
function count(){  
  var val   = $.trim($('textarea').val()), // Remove spaces from b/e of string
      words = val.replace(/\s+/gi, ' ').split(' ').length, // Count word-splits
      chars = val.length;                                  // Count characters
  if(!chars)words=0;

  $('#counter').html('<br>'+words+' words and '+ chars +' characters');
}
/* count(); */
/* $('textarea').on('input', count); */

function bubble(){
	$.post("index.php",
		$("#bubblein").serialize()).done(
		function(data){
			$('#bubbleout').html(data);
		}
	);
	$('#bubbleout').height($('textarea').height());
}
bubble();
$('textarea').on('input', bubble);

$('textarea').on('scroll', function() {
    $('#bubbleout').scrollTop($('textarea').scrollTop());
});

$( window ).resize(function() {
  $('#bubbleout').height($('textarea').height());
});

</script>

</html>

<?php

function Bubble($text){
    
    // Set variables
    $return = '';
    $text = explode("\n",$text);
    $currentline = '';
    $currentpage = 1;
    $currentpanel = 1;
    $currentbubble = 0;
    $prefix = '';
    $foundfirstpage = FALSE;
    $headingcount = 1;
    $pagebreakbefore = 'always';
    
    // Pick up parameters
    $parameters = BubbleConfiguration();
    
    foreach($text as $textline){
        $prefix = '';
        $textlinechars = str_split($textline);
        // Check for a new page or panel
        if ($textlinechars[0] == '#'){
            if (@$textlinechars[1] !== '#'){
                // New page
                $currentline = 'page';
		$foundfirstpage = TRUE;
                array_shift($textlinechars);
                $textline = trim(implode($textlinechars));
		if($currentpage == 1){
			if($headingcount > 1) { $return .= '</div>'; }
		}
                $return .= "<p><font size='+1'><strong>PAGE $currentpage</strong></font></p>";
                // Number the panels in the previous page
                $prevpage = $currentpage -1;
                if ($prevpage > 0){
                    $panelcount = $currentpanel -1 ;
		    if ($prevpage == 1 AND $headingcount == 1) { $pagebreakbefore = 'never'; } else { $pagebreakbefore = 'always'; }
                    $return = str_ireplace("<p><font size='+1'><strong>PAGE $prevpage</strong></font></p>", 
                                            "<p style='page-break-before:$pagebreakbefore'><font size='+1'><strong>PAGE $prevpage ($panelcount PANELS)</strong></font></p>", $return);
		    $return = str_ireplace("<p style='page-break-before:$pagebreakbefore'><font size='+1'><strong>PAGE $prevpage (0 PANELS)</strong></font></p>", "<p style='page-break-before:$pagebreakbefore'><font size='+1'><strong>PAGE $prevpage (SPLASH PAGE)</strong></font></p>", $return);
                }
                $currentpage++;
		
                $currentpanel = 1;
                $currentbubble = 0;
            } else {
                // New panel
                $currentline = 'panel';
                array_shift($textlinechars);
                array_shift($textlinechars);
                $textline = trim(implode($textlinechars));
                // $return .= "<p style='margin-top:10px;'><u>PANEL $currentpanel</u></p>";
                $prefix = "<u>PANEL $currentpanel:</u>&nbsp;";
                $currentpanel++;
            }
        } else {
            // Check for character or dialogue
            if ($textlinechars[0] == ' ' OR $textlinechars[0] == "\t"){
                if (@$textlinechars[1] == ' ' OR @$textlinechars[1] == "\t"){
                    // Speech bubble
                    $currentline = 'bubble';
                    array_shift($textlinechars);
                    array_shift($textlinechars);
                    $textline = trim(implode($textlinechars));
                    if($parameters['param_uppercasedialogue'] == 'on'){
                        $textline = strtoupper($textline);
                    }
                } else {
                    // Character
                    $currentline = 'character';
                    array_shift($textlinechars);
                    $textline = trim(implode($textlinechars));
                    $textline = strtoupper($textline);
                    $currentbubble++;
                }
            } else {
                if ($textlinechars[0] == '.'){
                    $currentline = 'private';
                } elseif($textlinechars[0] == '!'){
                    $currentline = 'comment';
                    array_shift($textlinechars);
                    $textline = trim(implode($textlinechars));
                } else {
		    if ($foundfirstpage) {
	                    $currentline = '';
		    } else {
			$currentline = 'heading';
		    }
                }
            }
        }
        
        if (trim($textline) == "\n" or trim($textline) == '') {
            if ($prefix !== ''){
                $return .= "<p style='margin-left:20px'>$prefix$textline</p>";
            } else {
                $textline = '<br>';
            }
        } else {
            $textline = BubbleMarkdown($textline);
            
            switch ($currentline){
                case 'character':
                    $return .= "<p style='margin:0;margin-left:40px;padding-top:5px;'><strong>$textline</strong> ($currentbubble)</p>";
                    break;
                case 'bubble':
                    $return .= "<p style='margin:0;margin-left:80px'>$textline</p>";
                    break;
                case 'comment':
                    $return .= "<p style='background:#DDD;'>$textline</blockquote>";
                    break;
                case 'private':
                    // Do not output a private line
                    break;
		case 'heading':
		    // if($headingcount == 1) { $return .= "<div style='margin-top:50%; text-align:center;'>"; }
		    if($headingcount == 1) { $return .= "<div style='text-align:center;'>"; }
		    $return .= "<h$headingcount style='margin:auto'>" . $textline . "</h$headingcount>";
		    if($headingcount < 3) { $headingcount++; }
		    break;
                case 'page':
                case 'panel':
                default:
                    $return .= "<p style='margin-left:20px'>$prefix$textline</p>";
                    break;
            }
        }
    }

    // Count panels in the final page
    $prevpage = $currentpage -1;
    $panelcount = $currentpanel -1 ;
    $return = str_ireplace("<p><font size='+1'><strong>PAGE $prevpage</strong></font></p>", 
                            "<br><p><font size='+1'><strong>PAGE $prevpage ($panelcount PANELS)</strong></font></p>", $return);
    $return = str_ireplace("<p><font size='+1'><strong>PAGE $prevpage (0 PANELS)</strong></font></p>", 
                            "<p><font size='+1'><strong>PAGE $prevpage (SPLASH PAGE)</strong></font></p>", $return);
    
    return $return;
}

function BubbleConfiguration(){
    
    $return = array();
    
    $return['param_uppercasedialogue'] = FALSE;
    
    foreach($return as $key => $value){
        if (isset($_POST[$key])){
            $return[$key] = $_POST[$key];
        }
    }
    
    return $return;
}

function BubbleMarkdown($textline){
    // Formats
    $formats = array('strong' => '**' , 'em' => '*', 'u' => '_');
    // Apply bold
    foreach($formats as $tag=>$marker){
        $on = FALSE;
        $markerlen = strlen($marker);
        
        $markerpos = strpos($textline,$marker);
        while (!($markerpos === FALSE)){
            if(!$on){ $tagonoff = "<$tag>"; $on = TRUE;} else { $tagonoff = "</$tag>"; $on = FALSE; }
            $textline = substr($textline, 0, $markerpos) . $tagonoff . 
                        substr($textline, $markerpos + $markerlen);
            $markerpos = strpos($textline,$marker);
        }
        if ($on) { $textline .= "</$tag>"; }
    }
    
    return $textline;
}

?>
