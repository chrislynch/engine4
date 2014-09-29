<?php

if (isset($_POST['bubbletext'])){
    if(@$_POST['op'] == 'Save script as TXT') {
	$title = explode("\n\r",$_POST['bubbletext']);
	$title = "bubble-" . date("Ymd") . "-" . date("Hms");
	header('Content-type: text/plain');
	header("Content-Disposition: attachment; filename=$title.txt");
	print $_POST['bubbletext'];
    } else {
	print Bubble($_POST['bubbletext']);
    }
    die();
}

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
            if ($textlinechars[1] !== '#'){
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
                if ($textlinechars[1] == ' ' OR $textlinechars[1] == "\t"){
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
		    if($headingcount == 1) { $return .= "<div style='margin-top:50%; text-align:center;'>"; }
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
