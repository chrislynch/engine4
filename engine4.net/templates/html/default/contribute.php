<?php
    print '<form action="?e4_action=contribute&e4_op=save&e4_contributionType=' . @$_REQUEST['e4_contributionType'] . '" method="POST">';
    print '<input name="e4_form_content_ID" type="hidden" value="' . @$content['ID'] . '">';
    print '<input name="e4_form_content_iscontent" type="hidden" value="' . @$content['iscontent'] . '">';
    if(isset($_REQUEST['e4_contributeType'])){
       include e4_findtemplate('forms/contribute/' . $_REQUEST['e4_contributeType'] . '.php');
    }
    print '</form>';
?>