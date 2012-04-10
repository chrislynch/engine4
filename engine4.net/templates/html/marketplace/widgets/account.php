<?php
    if ($data['user']['ID'] > 0){
        ?>
        <strong>My Account: </strong><br><a href="?e4_action=security&e4_security_op=deauthenticate">Log Out</a>
        <?php
    } else {  
        ?>
        <strong>My Account: </strong><br><a href="?e4_action=security&e4_security_op=authenticate">Log In/Register</a>
        <?
    }
?>
