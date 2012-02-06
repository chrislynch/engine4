<?php
/*
 * I am the view action
 * It is my job to load content, if there is not already some loaded.
 * I make sure the content is there so that it can be rendered.
 */

/*
 * All views and actions should start by loading up their defaults, if they are not already there.
 */


// TODO: This should not load anything if there has already been data loaded.
e4_data_search(array());

?>