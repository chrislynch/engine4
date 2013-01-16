
<h1>Content Explorer</h1>

<?php

admin_index();

function admin_index(){
    if (isset($_GET['directory'])){ $directory = $parent = $_GET['directory']; } else { $directory = '.'; $parent = '.';}
    if ($directory == '..'){ $directory = '.'; }

    print "<p>Exploring <strong>$directory</strong></p>";
    print '<table class="DataTable" style="width:100%">';
    print '<tr><th>&nbsp;</th><th>File/Directory</th></tr>';
    
    if ($directory !== '.'){
        $up = e::_dirup($directory);
        print "<tr><td>^</td><td><a href='admin?directory=$up'>Up ($up)</a></td></tr>";
    }
    
    $directories = scandir($directory);

    foreach($directories as $subdirectory){
        if (e::_isValidDirectory("$parent/$subdirectory")){    
            print "<tr><td>&gt;&gt;</td><td><a href='admin?directory=$parent/$subdirectory'>$subdirectory</a></td></tr>";
        }
    }
    foreach($directories as $subdirectory){
        if (e::_isValidFile($subdirectory,$parent)){    
            print "<tr><td></td><td><a href='admin/edit?file=$parent/$subdirectory'>$subdirectory</a></td></tr>";
        }    
    }
}



?>

</table>