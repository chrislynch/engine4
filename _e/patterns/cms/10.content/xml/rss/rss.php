<?php

$this->_loadplugin('config');
$this->content->html = xml_rss($this);
print $this->content->html;

function xml_rss(&$e){
    $return = '<?xml version="1.0"?>
                <!DOCTYPE rss>
                <rss version="0.91">
                <channel>';
    $return .= "<title>{$e->_config->get('Site.Name')}</title>";
    $return .= "<link>http://{$e->_domain()}</link>";
    $return .= "<description>{$e->_config->get('Site.Description')}</description>";
    
    $searchpaths = array('10.content/pages','10.content/posts');
    
    foreach($searchpaths as $searchpath){
        $items = $e->_search($searchpath);
        foreach($items as $item){
            $return .= '<item>';
                $return .= "<title>{$item->content->title}</title>";
                $return .= "<link>http://{$e->_domain()}/{$item->content->url}</link>";
                $return .= "<description></description>";
            $return .= '</item>';
        }
    }
    
    $return .= '</channel>
                </rss>';
    return $return;
}

?>
