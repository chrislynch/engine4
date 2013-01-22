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
    $return .= "<description>{$e->_config->get('Site.Description')}</description>\n";
    
    $searchpaths = array('10.content/pages','10.content/posts');
    
    foreach($searchpaths as $searchpath){
        $items = $e->_search($searchpath);
        foreach($items as $item){
            $return .= "<item>\n";
                $return .= "<guid>" . xml_rss_URL2GUID($item->content->url) . "</guid>\n";
                $return .= "<title>{$item->content->title}</title>\n";
                $return .= "<link>http://{$e->_domain()}/{$item->content->url}</link>\n";
                $return .= "<description></description>\n";
                $return .= "<pubDate>{$item->content->timestamp}</pubDate>\n";
            $return .= "</item>\n";
        }
    }
    
    $return .= '</channel>
                </rss>';
    return $return;
   
}

function xml_rss_URL2GUID($url){
    $guid = '';
    $url = str_split($url);
    foreach($url as $urlchar){
        $guid .= ord($urlchar);
    }
    $guid = md5($guid);
    return $guid;
}

?>
