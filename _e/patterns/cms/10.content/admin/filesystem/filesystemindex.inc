
<h1>File System Explorer</h1>

<?php

admin_index();

function admin_index(){
    if (isset($_GET['directory'])){ $directory = $parent = $_GET['directory']; } else { $directory = '.'; $parent = '.';}
    if ($directory == '..'){ $directory = '.'; }
    if ($directory == '.') { $uploaddirectory = '';} else { $uploaddirectory = $directory . '/';}
    if ($directory == '.'){
        $viewpath = '';
    } else {
        $viewpath = explode('/',$directory);
        array_shift($viewpath);
        array_shift($viewpath);
        $viewpath = implode('/',$viewpath);
    }
    
    // Make a new directory if one has been requested
    if (isset($_POST['mkdir'])){
        if (isset($_POST['dateprefix_mkdir'])){ $_POST['mkdir'] = date('YmdHi') . '.' . $_POST['mkdir']; }
        $_POST['mkdir'] = $_GET['directory'] . '/' . $_POST['mkdir'];
        print "Making {$_POST['mkdir']}";
        mkdir($_POST['mkdir'], 0777, TRUE);
        $_GET['directory'] = $_POST['mkdir'];
        unset($_POST['mkdir']);
    }
    
    // Make a new file if one has been requested
    if (isset($_POST['mkfile'])){
        file_put_contents($_GET['directory'] . '/' . $_POST['mkfile'],'');
        header('Location: ' . e::_basehref() . 'admin/filesystem/edit?file=' . $_GET['directory'] . '/' . $_POST['mkfile']);
        die();
    }
    
    // Delete a file or directory if we have asked to
    if (isset($_GET['rrm'])){
        print "Deleting {$_GET['rrm']}";
        rrm($_GET['rrm']);
    }
    
    // Upload a file if required
    if (isset($_FILES['upload'])){
        if ($_FILES['upload']['error'] == 0){
            print "Moving to $uploaddirectory{$_FILES['upload']['name']}";
            move_uploaded_file($_FILES['upload']['tmp_name'], $uploaddirectory . $_FILES['upload']['name']);
        } else {
            // Something went wrong uploading the file
        }
    }

    print "<p>Exploring <strong>$directory</strong>
            <a href='$viewpath'>(View)</a>
            </p>";
    print '<table class="DataTable" style="width:100%">';
    print '<tr><th>&nbsp;</th><th>File/Directory</th><th>Operations</th></tr>';
    
    if ($directory !== '.'){
        $up = e::_dirup($directory);
        print "<tr><td>^</td><td><a href='admin/filesystem?directory=$up'>Up ($up)</a></td></tr>";
    }
    
    $directories = scandir($directory);

    // Output the directories
    foreach($directories as $subdirectory){
        if (e::_isValidDirectory("$parent/$subdirectory")){    
            if ($subdirectory[0] !== '_' AND $subdirectory[0] !== '.'){
                print "<tr><td>&gt;&gt;</td><td><a href='admin/filesystem?directory=$parent/$subdirectory'>$subdirectory</a></td>
                <td><a href='admin/filesystem?directory=$directory&rrm=$parent/$subdirectory'>Delete</a></td></tr>";
            }
        }
    }
    // Output the files
    foreach($directories as $subdirectory){
        if (e::_isValidFile($subdirectory,$parent)){
            if ($subdirectory[0] !== '_' AND $subdirectory[0] !== '.'){
                if(e::_isBinaryFile("$parent/$subdirectory")){
                    print "<tr><td></td><td><a href='$parent/$subdirectory' target='_blank'>$subdirectory</a></td>
                    <td><a href='admin/filesystem?directory=$directory&rrm=$parent/$subdirectory'>Delete</a></td></tr>";
                } else {
                    print "<tr><td></td><td><a href='admin/filesystem/edit?file=$parent/$subdirectory'>$subdirectory</a></td>
                    <td><a href='admin/filesystem?directory=$directory&rrm=$parent/$subdirectory'>Delete</a></td></tr>";
                }
            }
        }    
    }
    
    print "</table>";
    
    if (TRUE){ //TODO: Check writability of directory
        ?>
        <hr>
        <form action="admin/filesystem?directory=<?=$directory?>" method="POST">
            <label>New Path/Directory:</label>
            <input name="mkdir" type="text" size="255" style="width:120px">
            <label>Date Stamp:</label>
            <input name="dateprefix_mkdir" type="checkbox">
            <input type="submit" value="Create new path">
        </form>
        <hr>
        <form action="admin/filesystem?directory=<?=$directory?>" method="POST">
            <label>New Text File:</label><input name="mkfile" type="text" size="255" style="width:120px">
            <input type="submit" value="Create new file">
        </form>
        <hr>
        <form action="admin/filesystem?directory=<?=$directory?>" method="POST" enctype="multipart/form-data">
            <label>Upload File:</label><input name="upload" type="file">
            <input type="submit" value="Create new file">
        </form>
        <?php
    } else {
        print "Cannot write to $directory";
    }
}

function rrm($dir) {
   if (is_dir($dir)) {
     $objects = scandir($dir);
     foreach ($objects as $object) {
       if ($object != "." && $object != "..") {
         if (filetype($dir."/".$object) == "dir") rrm($dir."/".$object); else unlink($dir."/".$object);
       }
     }
     reset($objects);
     rmdir($dir);
   } else {
       unlink($dir);
   }
 } 

?>