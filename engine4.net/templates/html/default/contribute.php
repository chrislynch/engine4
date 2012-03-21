<?php
    print '<form action="?e4_action=contribute&e4_op=save&e4_contributeType=' . @$_REQUEST['e4_contributeType'] . '" method="POST">';
    if(isset($_REQUEST['e4_contributeType'])){
       include e4_findtemplate('forms/contribute/' . $_REQUEST['e4_contributeType'] . '.php');
    }
    print '</form>';
?>