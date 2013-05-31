<?php

class _grid{
    
    private $gridposition;
    private $gridsize;
    
    public function __construct(&$e){
        $this->e =& $e;
        $this->gridposition = 0;
        $this->gridsize = 0;
    }
    
    public function startgrid($gridsize){
        $this->gridsize = $gridsize;
        $this->gridposition = 1;
    }
    
    public function alphaomega(){
        if ($this->gridposition == 1){
            $this->gridposition += 1;
            return 'alpha';
        } elseif ($this->gridposition == $this->gridsize) {
            $this->gridposition = 1;
            return 'omega';
        } else {
            $this->gridposition += 1;
            return '';
        }
    }
}
?>
