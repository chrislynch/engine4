<?php

class _cookies {
    /* Configuration helper class */
    
    var $config;
    
    public function __construct(){
        
    }

    public function cookiewarning(){
        $cookie_warning_text = '<div style="height: 80px;">&nbsp;</div>
                                                                <div id="warp_cookie_warning" 
                                                                    style="display: block;
                                                                            position: fixed;
                                                                            text-align: center;
                                                                                z-index:1000;
                                                                            bottom: 0;
                                                                            left: 0;
                                                                            width: 100%;
                                                                            color: #999999;
                                                                            clear: both;
                                                                            height: 35px;
                                                                            min-width: 960px;
                                                                            border-top-style: solid;
                                                                            border-top-width: 1px;
                                                                            border-top-color: #b5b6b5;
                                                                            background-repeat: repeat-x;
                                                                            border-right-style: solid;
                                                                            border-left-style: solid;
                                                                            border-right-width: 1px;
                                                                            border-left-width: 1px;
                                                                            border-right-color: #b5b6b5;
                                                                            border-left-color: #b5b6b5;
                                                                            background-color: #e7e7e7;
                                                                            padding-bottom: 10px;">
                                                This website uses cookies to remember your preferences and improve your browsing experience.
                                                By continuing to use this website, you are consenting to accept all of our cookies. <br>
                                                <a href="?cookies=YES">Click here to let us remember your preference and hide this message.</a>
                                                </div>'; 
        
        if (isset($_GET['cookies'])){
            setcookie('eu_cookie_consent',$_GET['cookies'],(time()+60*60*24*30*6),'/');
            $cookie_warning_text = '';
        }
        if($_COOKIE['eu_cookie_consent'] == 'YES'){
            $cookie_warning_text = '';
        }
        
        return $cookie_warning_text;
    }
    
}



?>
