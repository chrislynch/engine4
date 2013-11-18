<?php

class _config {
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
        $this->config['site.name'] = 'An engine4 powered website';
        
        $this->config['mysql.server'] = '127.0.0.1';
        $this->config['mysql.user'] = 'root';
        $this->config['mysql.password'] = '';
        $this->config['mysql.database'] = 'localhost';
        
        $this->config['smtp.host'] = '';
        $this->config['smtp.port'] = '25';
        $this->config['smtp.user'] = '';
        $this->config['smtp.password'] = '';
        $this->config['smtp.from.Address'] = 'info@ecommercecentric.co.uk';
        $this->config['smtp.from.Name'] = 'eCommerce Centric';
        $this->config['smtp.reply.Address'] = 'info@ecommercecentric.co.uk';
        $this->config['smtp.reply.Name'] = 'eCommerce Centric';
    }
    
    public function save(){
        
    }
    
}
?>
