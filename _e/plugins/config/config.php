<?php

class config {
    /* Configuration helper class */
    
    var $config;
    
    public function __construct(){
        $this->load();
    }
    
    public function get($configName,$defaultValue = ''){
        if (isset($this->config[$configName])){
            return $this->config[$configName];
        } else {
            return $defaultValue;
        }
    }
    
    public function set($configName,$newValue){
        $this->config[$configName] = $newValue;
    }
    
    public function load(){
        $this->config = array();
        $this->config['Site.Name'] = 'An engine4 powered website';
    }
    
    public function save(){
        
    }
    
}
?>
