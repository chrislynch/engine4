
<h1>Content Explorer</h1>

<?php

admin_index();

function admin_index(){
    // Make a new directory if one has been requested
    if (isset($_POST['mkdir'])){
        $_POST['mkdir'] = $_GET['directory'] . '/' . $_POST['mkdir'];
        print "Making {$_POST['mkdir']}";
        mkdir($_POST['mkdir'], 0777, TRUE);
        $_GET['directory'] = $_POST['mkdir'];
        unset($_POST['mkdir']);
    }
    
    // Make a new file if one has been requested
    if (isset($_POST['mkfile'])){
        file_put_contents($_GET['directory'] . '/' . $_POST['mkfile'],'');
        header('Location: admin/edit?file=' . $_GET['directory'] . '/' . $_POST['mkfile']);
        die();
    }
    
    // Delete a file or directory if we have asked to
    if (isset($_GET['rrm'])){
        print "Deleting {$_GET['rrm']}";
        rrm($_GET['rrm']);
    }
    
    if (isset($_GET['directory'])){ $directory = $parent = $_GET['directory']; } else { $directory = '.'; $parent = '.';}
    if ($directory == '..'){ $directory = '.'; }

    print "<p>Exploring <strong>$directory</strong></p>";
    print '<table class="DataTable" style="width:100%">';
    print '<tr><th>&nbsp;</th><th>File/Directory</th><th>Operations</th></tr>';
    
    if ($directory !== '.'){
        $up = e::_dirup($directory);
        print "<tr><td>^</td><td><a href='admin?directory=$up'>Up ($up)</a></td></tr>";
    }
    
    $directories = scandir($directory);

    foreach($directories as $subdirectory){
        if (e::_isValidDirectory("$parent/$subdirectory")){    
            print "<tr><td>&gt;&gt;</td><td><a href='admin?directory=$parent/$subdirectory'>$subdirectory</a></td>
            <td><a href='admin?directory=$directory&rrm=$parent/$subdirectory'>Delete</a></td></tr>";
        }
    }
    foreach($directories as $subdirectory){
        if (e::_isValidFile($subdirectory,$parent)){    
            print "<tr><td></td><td><a href='admin/edit?file=$parent/$subdirectory'>$subdirectory</a></td>
            <td><a href='admin?directory=$directory&rrm=$parent/$subdirectory'>Delete</a></td></tr>";
        }    
    }
    
    print "</table>";
    
    if (TRUE){ //TODO: Check writability of directory
        ?>
        <hr>
        <form action="admin?directory=<?=$directory?>" method="POST">
            <label>New Path:</label><input name="mkdir" type="text" size="255" style="width:120px">
            <input type="submit" value="Create new path">
        </form>
        <hr>
        <form action="admin?directory=<?=$directory?>" method="POST">
            <label>New File:</label><input name="mkfile" type="text" size="255" style="width:120px">
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