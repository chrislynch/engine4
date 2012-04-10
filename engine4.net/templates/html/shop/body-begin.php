<div id="container-container">
    <div class="container">
        <div id="header-top" class="span-24 last">
            <div id="header-top-left" class="span-12">
                <div id="header-top-left-logo" class="span-12 last">
                    <a href="@@configuration.basedir@@" title="Go to the <?=@$data['page']['head']['sitename'];?> homepage"><?=@$data['page']['head']['sitename'];?></a>
                </div>
            </div>
            <div id="header-top-right" class="span-12 last">
                <div id="header-top-right-search" class="span-12 last">
                    <form action="@@configuration.basedir@@search" method="GET">
                        <input type="text" name="e4_search">
                        <input type="submit" value="Search">
                    </form>
                </div>
                <div id="header-top-right-account" class="span-12 last">
                    Welcome visitor. 
                    Would you like to <a href="@@configuration.basedir@@security/authenticate" title="Click here to log in">log in</a>
                    or <a href="@@configuration.basedir@@security/register" title="Click here to create an account">register</a>?
                </div>
                <div id="header-top-right-menu" class="span-12 last">
                    
                </div>    
            </div>
        </div>
        <div id="header-bottom" class="span-24 last">
            <div id="header-bottom-left" class="span-12">
                <div id="header-bottom-left-menu" class="span-12 last">
                    &nbsp;
                </div>
            </div>
            <div id="header-bottom-right" class="span-12 last">
                <a href="@@configuration.basedir@@cart/view"><strong>My Basket: </strong></a>
                <?=@$data['cart']['totalitems'];?> items&nbsp;/&nbsp;&pound;<?=@$data['cart']['totalvalue'];?>
                <a href="@@configuration.basedir@@checkout" class="checkout">Click to Checkout</a>
            </div>
        </div>
        
        <div id="banner-top" class="span-24 last">
            
        </div>
        <div id="banner-bottom" class="span-24 last">
            
        </div>
        
        <div id="content" class="span-24 last">
            
        
    