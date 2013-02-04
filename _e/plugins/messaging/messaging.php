<?php

class _messaging {
    
    var $messages = array();
    
    function addMessage($message,$type = 0){
        if ($type == -9){
            die($message);
        } else {
            $this->messages[$message] = array('message'=>$message,'type'=>$type);
        }
    }
    
}
?>
