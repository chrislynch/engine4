<?php
// Log the user out
if (isset($_GET['logout'])){
    
}

// Apply security checks
if (@$_GET['q'] !== 'admin/authenticate'){
    if (strpos(@$_GET['q'],'admin') === 0){
        // We are in the admin directory.
        // Time to apply some security!
        if (isset($_COOKIE['user'])){
            // All is well, the admin cookie is set.
        } else {
            header('Location: admin/authenticate',403);
        }
    }    
} else {
    if (isset($_POST['user']) && isset($_POST['password'])){
        // Validate that the username and password match
        if ($_POST['user'] == 'admin' && $_POST['password'] == 'spider20'){
            // Authentication test passed
            setcookie('user',$_POST['user'],0,'/');
            header('Location: admin',301);
        } else {
            header('Location: admin/authenticate',403);
        }
    }
}

?>