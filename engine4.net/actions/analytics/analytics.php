<?php
 
/*
 * Analytics is the last action that will ever be called.
 * It records what we did, and traps stats.
 */

function e4_action_analytics_analytics_go(&$data){
    /*
     * Record what we have looked at. Record what we did.
     */
    foreach($data['page']['body']['content'] as $ID=>$content){
        e4_action_analytics_saveStat('View', 1, $ID);
    }
}

function e4_action_analytics_saveStat($stat,$value,$ID = 0){
    /*
     * Record an individual stat.
     * If no ID is given, try to derive one.
     */
    if ($ID == 0){
        if (isset($_REQUEST['e4_ID']) && $_REQUEST['e4_ID'] > 0){
            $ID = $_REQUEST['e4_ID'];
        }
    }
    // Build the query
    $statQuery = 'INSERT INTO e4_stats
                    SET year = YEAR(NOW()),
                        month = MONTH(NOW()),
                        day = DAY(NOW()),
                        ID = ' . $ID . ',
                        Stat = "' . $stat . '",
                        Value = ' . $value . '
                   ON DUPLICATE KEY UPDATE Value = Value + ' . $value;
    // Run query.
    e4_db_query($statQuery);
}
?>