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
    $data['user']['ID'] = cookie_get('userid',0);
    if ($data['user']['ID'] > 0){
        $data['user'] = e4_data_load($data['user']['ID'],FALSE,FALSE);
    }

    /*
    * @todo Now that we know who the current user is, ascertain if this user can access the page that they are looking at.
    */
    // Hard code controls around "admin", to prevent someone removing this from the database by mistake.
    if(in_array('admin/admin.php', $data['actions'])){
        // The current user must be an administrator to do this.
        if(!(@$data['configuration']['install'] == TRUE)){
            if(!e4_security_user_hasRole($data['user'], 'Administrator')){
                e4_goto('?e4_action=security&e4_security_op=authenticate', 403);
            }
        }
    }
    
    if(isset($_REQUEST['e4_security_op'])){
        switch($_REQUEST['e4_security_op']){
            case 'authenticate':
                e4_action_security_security_authenticate($data);
                break;
            
            case 'deauthenticate':
                // Log out of the system.
                cookie_set('userid', '');
                e4_goto('?');
                break;
            
            case 'register':
                // Registation is essentially a content creation step.
                e4_action_security_security_register($data);
                break;
        }
    }
}

function e4_security_user_hasRole($user,$role){
    /*
     * Look into a user array and see if a certain role exists.
     */
    if (isset($user['data']['roles'])){
        // We have a user and they have roles, so check to see if this role exists.
        if(in_array($role, $user['data']['roles'])){
            return true;
        } else {
            return false;
        }
    } else {
        // The user may only have ONE role. Check for that.
        if (isset($user['data']['role'])){
            if ($user['data']['role'] == $role){
                return true;
            } else {
                return false;
            }
        }
        // User has NO roles. Return FALSE
        return false;
    }
}

function e4_action_security_security_authenticate(&$data){
    if (isset($_REQUEST['e4_form_security_username']) && isset($_REQUEST['e4_form_security_password'])){
        // Load a list of users who match this name
        $users = e4_data_search(array('name'=>$_REQUEST['e4_form_security_username'],'type'=>'user'),FALSE,FALSE);
        if (sizeof($users) > 0){
            foreach($users as $user){
                if ($user['data']['password'] = $_REQUEST['e4_form_security_password']){
                    // This is our user, and the password is good.
                    $data['user'] = $user;
                    cookie_set('userid',$user['ID']);
                    e4_message('Welcome back ' . $user['name']);
                    if (isset($_REQUEST['e4_destination_action'])){
                        e4_goto('?e4_action=' . $_REQUEST['e4_destination_action']);
                    } else {
                        e4_goto('?e4_action=my');
                    }
                    break;
                } else {
                    // This is not the right user
                    e4_message('Invalid password for user "' . $user['name'] . '"');
                    $data['configuration']['renderers']['all']['templates'][0] = 'forms/security/authenticate.php';
                    break;
                }
            }    
        } else {
            e4_message('Invalid account. There is no such user as "' . $_REQUEST['e4_form_security_username'] . '"');
            $data['configuration']['renderers']['all']['templates'][0] = 'forms/security/authenticate.php';
        }
    } else {
        // This is a request for a log in form
        $data['configuration']['renderers']['all']['templates'][0] = 'forms/security/authenticate.php';
    }
}

function e4_action_security_security_register(&$data){
    // Registration is essentially the act of creating a user account, which is a data-type/content form.
    
    // Create a new user object
    $newUser = e4_data_new('user');
    
    // Attempt to save data to it
    include_once e4_findinclude('actions/admin/admin.php');
    $userID = e4_admin_admin_formData_save($newUser);
    
    if ($userID > 0){
        // We have registered.
        // Automatically log the user in and drop them off at their page
        cookie_set('userid',$userID);
        e4_goto('?e4_action=my');
    } else {
        
    }
}

?>