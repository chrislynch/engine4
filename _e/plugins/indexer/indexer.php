<?php

class _indexer{
    
    private $e;
    
    public function __construct(&$e){
        $this->e =& $e;
    }
    
    public function indexfs($targetdir = ''){
        // Drop existing indexes
        // Index all the filesystem content in the _content directory of our domain.
        // We only index _body content, as this is where "real" content lives
        if ($targetdir == '') { 
            $targetdir = '10.content/'; 
            // $k->__db->update('ALTER TABLE idx_index AUTO_INCREMENT = 1');
            $this->e->_db->delete('TRUNCATE idx_index');
            $this->e->_db->delete('TRUNCATE idx_search');
            $this->e->_db->delete('TRUNCATE idx_data');
        }
        
        $this->e->trace("Scanning directory $targetdir");
        $targets = scandir($targetdir);
        foreach($targets as $target){
            if ($target !== '.' && $target !== '..'){
                if (is_dir($targetdir . $target)){
                    // Recurse into this directory to check things out.
                    $this->e->trace('Heading down into' . $targetdir . $target . '/');
                    $this->indexfs($targetdir . $target . '/');
                } else {
                    // This is a real file.
                    $this->e->traceDepth++;
                    $this->e->trace("Found $targetdir$target");
                    $filetype = strtolower(e::_popget('.', $target));
                    if ($filetype == 'html'){
                        // This is a real piece of loadable content
                        $this->e->trace("Loading $target");

                        $savetargetdir = explode('/',$targetdir);
                        array_shift($savetargetdir); array_shift($savetargetdir); array_shift($savetargetdir);
                        $savetargetdir = implode('/',$savetargetdir);

                        $savetarget = str_ireplace('.html', '', $target);

                        $thingID = $this->e->_db->insert("INSERT INTO idx_index(ID,path) values(0,'$targetdir')");

                        $thing = new e();
                        // $thing->_open($targetdir . $savetarget);
                        $thing->_open($targetdir);
                        
                        // Build full text search of HTML and XML data
                        // TODO: Include indexing of non-XML items outside of .html file (like .faqs file)
                        $searchtext = $thing->content->html;
                        $searchtext = strip_tags($searchtext);
                        $searchtext = $this->e->_db->escape($searchtext);
                        $searchXML = '';
                        if (isset($thing->content->xml)){
                            $searchXML = $thing->content->xml->asXML();
                            $searchXML = str_ireplace(chr(10), '', $searchXML);
                            $searchXML = str_ireplace(chr(13), '', $searchXML);
                            $searchXML = $this->e->_db->escape($searchXML);
                        }

                        $this->e->_db->insert("INSERT INTO idx_search(ID,search_text,search_xml) VALUES($thingID,'$searchtext','$searchXML')");

                        // Now index the XML into the idx_XML table
                        $elements = get_object_vars($thing);
                        
                        foreach($elements['content'] as $element => $elementValue){
                            if(is_object($thing->content->$element)){
                                if(get_class($thing->content->$element) == 'SimpleXMLElement'){
                                    $this->e->trace("Indexing XML from $element");
                                    $this->indexXMLelement($thingID, $thing->content->$element);
                                }
                            }   
                        }

                        // Timestamp the item
                        $this->e->_db->insert("INSERT INTO idx_XML SET ID = $thingID, XPath = '/timestamp', Value = '{$thing->content->timestamp}'");


                    } else {
                        $this->e->trace("Ignoring $target");
                    }
                }
            }
        }
        $this->e->trace("Leaving directory $targetdir");
    }

    private function indexXMLelement($ID,$xmlElement,$xpath = ''){
        // If the XML element has children, index those.
        // If not, index the element itself.
        $xpath .= '/' . $xmlElement->getName();
        $xname = $xmlElement->getName();
        $xvalue = trim((string) $xmlElement);
        // $xvalue = $k->__db->escape($xmlElement);
        
        if (strlen($xvalue) > 0){
            // $this->trace('Indexing ' . $xpath . ' as ' . (string) $xmlElement);
            $this->e->_db->insert("INSERT INTO idx_XML(ID,XPath,Name,Value) VALUES($ID,'$xpath','$xname', '$xvalue')");
        }
        
        foreach($xmlElement->children() as $child){
            // $this->trace($xpath . ' has children');
            $this->indexXMLelement($ID, $child, $xpath);
        }
    }
}


?>
