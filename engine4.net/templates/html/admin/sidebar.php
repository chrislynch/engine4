<div class="span-8 last">
	<h2 class="SectionHeading">Help</h2>
        <?php
            if (isset($_REQUEST['e4_op'])){
                include e4_findtemplate('sidebar-content/' . $_REQUEST['e4_action'] . '-' . $_REQUEST['e4_op'] . '.php');
            } else {
                include e4_findtemplate('sidebar-content/' . $_REQUEST['e4_action'] . '.php');
            }
            
        ?>
</div>
</div>
