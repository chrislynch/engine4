
<h1>File Editor</h1>

<p>Editing <strong><?=$_GET['file']?></strong> (<a href="admin?directory=<?= e::_dirup($_GET['file']) ?>">Back)</a></p>

<?php

if ($_GET['file']){
    admin_file_edit();
} else {
    
}

function admin_file_edit(){

    // Save the file if we need to
    
    if (isset($_GET['save'])){
        // Save the contents of the file
        if (!(file_put_contents($_GET['file'], $_POST['filecontents']) === FALSE)){
            unset($_POST['filecontents']);
        } else {
            print "<p><strong>Warning: Could not update file</p>";
        }
    }

    // Load the contents of the file
    if (isset($_POST['filecontents'])){
        $filecontents = $_POST['filecontents'];
    } else {
        $filecontents = file_get_contents($_GET['file']);
    }

    // Show the editor/viewer

    if (is_writable($_GET['file'])){
    ?>
        <form action="admin/edit?save&file=<?=$_GET['file']?>" method="POST">
            <textarea name="filecontents" rows='30' style='width:100%'><?=htmlentities($filecontents)?></textarea><br>
            <input type="submit" value="Save Changes">
        </form>
    <?php
    } else {
        print '<pre>' . htmlentities($filecontents) . '</pre>';
    }
    
}

function admin_template_edit(){

    // Save the file if we need to
    
    if (isset($_GET['save'])){
        $save = array();
        foreach($_POST as $key => $value){
            
        }
        
        foreach($save as $filetype => $data){
            
        }
    }

    // Show the editor/viewer

    
}

?>

