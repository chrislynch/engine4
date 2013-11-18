<?php

class _pager {
    
    private $e;
    
    public function __construct(&$e){
        $this->e =& $e;
        
    }
    
    public function pager($pages = 1,$param = 'page',$ulclass='pager',$liclass='page',$liselectedclass='selectedpage'){
        // Decide what page we are on
        $page = 1;
        if (isset($_GET['page'])){
            if(is_numeric($_GET['page'])){
                $page = strval($_GET['page']);
            } else {
                $page = 1;
            }
        }
        
        // Output a list/pager control
        $return = '<ul';
        if ($ulclass !== ''){ $return .= ' class="' . $ulclass . '">'; } else { $return .= '>'; }
 
        for ($index = 1; $index <= $pages; $index++) {
            $return .= '<li';
            if($index == $page){
                if ($liselectedclass !== ''){ $return .= ' class="' . $liselectedclass . '">'; } else { $return .= '>'; }
            } else {
                if ($liclass !== ''){ $return .= ' class="' . $liclass . '">'; } else { $return .= '>'; }
            }
            if($index !== $page){
                $return .= '<a href="' . $this->e->qp() . '?page=' . $index;
                if (isset($_GET['keywords'])){ $return .= '&keywords=' . $_GET['keywords']; }
                $return .= '">';
            }
            $return .= $index;
            if($index !== $page){
                $return .= '</a>';
            }
            $return .= '</li>';
        }
        
        $return .= '</ul>';
        
        return $return;
    }
}  
?>    