
    <div class="container">
        <div id="header" class="span-24 last">
            <div id="sitename" class="span-18">
                <a id="sitename-sitename" href="@@configuration.basedir@@">
                <img src="@@configuration.basedir@@www.christopherwilliamlynch.com/logo.png">
                <?=@$data['page']['body']['H1']?></a><br>
                <span id="sitename-slogan">The home of great eCommerce</span>
            </div>
            <div id="header-account" class="span-3">
                <?php include e4_findtemplate('widgets/account.php'); ?>
            </div>
            <div id="header-cart" class="span-3 last">
                <?php include e4_findtemplate('widgets/cart.php'); ?>
            </div>
        </div>
        <div id="primary-links" class="span-24 last">
            <div id="primary-links-menu" class="span-18">
                <?php include e4_findtemplate('widgets/menu.php'); ?>
            </div>
            <div id="primary-links-search" class="span-6 last">
                <form action="@@configuration.basedir@@search" method="GET">
                    <strong>Search: </strong>
                    <input type="text" name="e4_search" size="14">
                    <input type="submit" value="GO">
                </form>
            </div>
        </div>
        <div id="content" class="span-24 last">
            
    
    