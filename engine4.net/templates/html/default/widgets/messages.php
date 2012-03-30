<?php 
    if (isset($data['page']['messages'])){
        foreach($data['page']['messages'] as $message){
                print '<div class="' . $message['type'] . 'Message">';
                print $message['type'] . ': ' . $message['message'];
                print '</div>';
        }
        // As we have now output the messages, it is safe to destroy them.
        $data['page']['messages'] = array();
    }
?>