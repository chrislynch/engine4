
    <div class="container">
        <div id="header" class="span-24 last">
            <div id="sitename" class="span-18"><a href="@@configuration.basedir@@"><?=@$data['page']['body']['H1']?></a></div>
            <div id="cart" class="span-6 last">
                <?php include e4_findtemplate('widgets/cart.php'); ?>
            </div>
        </div>
        <div id="menu-primary" class="span-24 last">
            <?php include e4_findtemplate('widgets/menu.php'); ?>
        </div>
        <div id="search" class="span-24 last">
            <form action="@@configuration.basedir@@search" method="GET">
                <strong>Search: </strong>
                <input type="text" name="e4_search" size="130">
                <input type="submit" value="Search">
            </form>
        </div>
        <div id="content" class="span-18">
            
    
    