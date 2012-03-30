<div class="span-8 append-2">
    <h2>My Content</h2><ul>
    <?php
        if (isset($content['linkages']['from']['owner'])){
            foreach($content['linkages']['from']['owner'] as $linkID=>$linkedItem){
                if ($linkedItem['iscontent'] == TRUE){
                    if (isset($linkedItem['name'])){
                        print '<li><a href="' . $linkedItem['link'] . '">' . $linkedItem['name'] . '</a>';
                        if (!@$linkedItem['status']==1){
                            print ' (Unpublished or inactive)';
                        }
                        print '</li>';
                    }
                }
            }
        } else {
            print '<p>You haven\'t created any lists yet ...<br>
                    <a href="@@configuration.basedir@@contribute&e4_contributeType=listpointlist&e4_contribute_op=create">Click here to create your first list</a></p>';
        }
    ?>
    </ul>
</div>

<div class="span-12 last">
    <h2>My Purchases</h2><ul>
    <?php
        if (isset($content['linkages']['from']['purchaser'])){
            foreach($content['linkages']['from']['purchaser'] as $linkID=>$linkedItem){
                if (isset($linkedItem['name'])){
                    print '<li><a href="' . $linkedItem['link'] . '">' . $linkedItem['name'] . '</a>';
                    if (!@$linkedItem['status']==1){
                        print ' (Unpublished or inactive)';
                    }
                    if(isset($linkedItem['data']['files']['lists']['primary'])){
                        print '&nbsp;<a href="' . $linkedItem['data']['files']['lists']['primary']['path'] . '">Download now</a>';
                    }
                    print '</li>';
                }
            }
        }
    ?>
    </ul>
</div>
