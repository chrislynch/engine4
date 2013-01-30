<?php

class _messaging {
    
    var $messages = array();
    
    function addMessage($message,$type = 0){
        $this->messages[$message] = array('message'=>$message,'type'=>$type);
    }
    
}
?>
