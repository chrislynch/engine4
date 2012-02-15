<?php
/*
 * Security module. Make sure we are where we are supposed to be, and nowhere else.
 */

function e4_action_security_security_go(&$data){
    /*
    * Ascertain if the current user is a logged in user or not
    * Security is the only part of the system, at the moment, that uses cookies.
    * Hence the cookie shortcut functions are here.
    */

    $data['user'] = array();
    $data['user']['id'] = cookie_get('userid',0);
    if ($data['user']['id'] > 0){
        $data['user'] = e4_data_load($data['user']['id'],FALSE);
    }

    /*
    * @todo Now that we know who the current user is, ascertain if this user can access the page that they are looking at.
    */
    // Hard code controls around "admin", to prevent someone removing this from the database by mistake.
    if(in_array('admin/admin.php', $data['actions'])){
        // The current user must be an administrator to do this.
        if(!e4_security_user_hasRole($data['user'], 'Administrator')){
            // e4_goto('?e4_action=security&e4_op=authenticate', 403);
        }
    }
    
    /*
     * After processing security constraints, look to see if there are any security operations taking place
     * @todo: The code below raises the question of whether we need a different e4_op for each action. Probably we do!
     */
    if(isset($_REQUEST['e4_op'])){
        switch($_REQUEST['e4_op']){
            case 'authenticate':
                if (isset($_REQUEST['username'])){
                    // This is an attempt to log in
                } else {
                    // This is a request for a log in form
                    $data['configuration']['renderers']['all']['templates'][0] = 'forms/security/authenticate.php';
                }
                break;
            
            case 'deauthenticate':
                // Log out of the system.
                cookie_set('userid', '');
                e4_goto('?');
                break;
        }
    }
}


function cookie_set($cookiename,$cookievalue){
    $cookiename = 'e4_' . e4_domain() . '_' . $cookiename;
    $cookiename = str_ireplace('.', '_', $cookiename);
    setcookie($cookiename,$cookievalue,0,'/');
    $_REQUEST['cookie_' . $cookiename] = $cookievalue; // Tuck cookie value here in case it is needed during this page render 
}

function cookie_get($cookiename,$defaultvalue = '',$widget = FALSE){
    if (!$widget) {$cookiename = 'e4_' . e4_domain() . '_' . $cookiename;}
    $cookiename = str_ireplace('.', '_', $cookiename);

    if (isset($_REQUEST['cookie_' . $cookiename])){
        return $_REQUEST['cookie_' . $cookiename];
    } else {
        if (isset($_COOKIE[$cookiename])){
                return $_COOKIE[$cookiename];
        } else {
                return $defaultvalue;	
        }
    }
}

function e4_security_user_hasRole($user,$role){
    /*
     * Look into a user array and see if a certain role exists.
     */
    if (isset($user['roles'])){
        // We have a user and they have roles, so check to see if this role exists.
        if(in_array($role, $user['roles'])){
            return true;
        } else {
            return false;
        }
    } else {
        // User has NO roles. Return FALSE
        return false;
    }
}

?>