<?php

class _drupal{

        public $pages;
        public $pageLength = 10;
    
        private function calcPageLimit(){
            // Work out which page we are on.
            if (isset($_GET['page'])){
                if(is_numeric($_GET['page'])){
                    $page = strval($_GET['page']);
                    $limit = (($page -1) * $this->pageLength) . ',' . (($page) * $this->pageLength);
                }
            } else {
                $limit = $this->pageLength;
            }
            
            return $limit;
        }
        
	public function __construct(&$e){
            $this->e =& $e;
            include '_admin/sites/default/settings.php';
            $e->_loadPlugin('db');
            $e->_config->set('mysql.server',$databases['default']['default']['host']);
            $e->_config->set('mysql.user',$databases['default']['default']['username']);
            $e->_config->set('mysql.password',$databases['default']['default']['password']);
            $e->_config->set('mysql.database',$databases['default']['default']['database']);
	}

	public function drupal_load_node($nid){
		$select = "SELECT n.*, u.alias as url ";
		$from = " from url_alias u
				  join node n on n.nid = reverse(substring_index(reverse(u.source),'/',1)) 
											AND substring_index( u.source, '/', 1 ) = 'node'
				  ";
		$i = 1;
				
		$node = $this->e->_db->query("SELECT * FROM node WHERE nid = $nid");
		
		if ($node = mysql_fetch_assoc($node)){
			$fields = $this->e->_db->query("select fci.field_name,fc.module,fc.type
					from field_config_instance fci
					join field_config fc on fc.id = fci.field_id
					where bundle = '{$node['type']}'");
			
			while($field = mysql_fetch_object($fields)){
				switch($field->type){
					case 'text':
                                        case 'list_text':
						$select .= ",f$i.{$field->field_name}_value ";
						$from .= " LEFT OUTER join field_data_{$field->field_name} f$i ON f$i.entity_id = n.nid ";
						break;
					case 'text_with_summary':
						$select .= ",f$i.{$field->field_name}_value, f$i.{$field->field_name}_summary";
						$from .= " LEFT OUTER join field_data_{$field->field_name} f$i ON f$i.entity_id = n.nid ";
						break;
					case 'file':
						// $select .= ",GROUP_CONCAT(fm$i.filename,'|') as {$field->field_name}_filename";
                                                $select .= ",GROUP_CONCAT(REPLACE(fm$i.uri,'public://',''),'|') as {$field->field_name}_filename";
						$from .= " LEFT OUTER join field_data_{$field->field_name} f$i ON f$i.entity_id = n.nid
								   LEFT OUTER join file_managed fm$i ON fm$i.fid = f$i.{$field->field_name}_fid";
						break;
				}
				$i++;	// Increase join counter.
			}
			
			$node = $this->e->_db->query("$select $from WHERE n.nid = $nid");
			$node = mysql_fetch_assoc($node);
			
			// Prepare body to match files
			if(isset($node['body_value'])){
				foreach($node as $key => $value){
					$keypart = explode('_',$key);
					$keypart = array_pop($keypart);
					if ($keypart == 'filename') {
						$files = explode('|',$value);
						foreach($files as $file){
							if (strlen(trim($file)) > 0){
								$node['body_value'] = str_ireplace($file,'_admin/sites/default/files/' . $file,$node['body_value']);
							}
						}
					}
				}
			}
			
			return $node;
		
		}
		
	}
	
	public function drupal_load_nodearray($nids = array()){
		$return = array();
		foreach($nids as $nid){
			$node = $this->drupal_load_node($nid);
			$return[$node['nid']] = $node;
		}
		return $return;
	}
	
	private function drupal_find($where = '', $params = array()){
		
                $select = "SELECT n.*, u.alias as url ";
		$from = " from url_alias u
				  join node n on n.nid = reverse(substring_index(reverse(u.source),'/',1)) 
										 AND substring_index( u.source, '/', 1 ) = 'node'
				  left outer join weight_weights ww ON ww.entity_id = n.nid ";
		                        
                if(!isset($params['join'])) { $params['join'] = '';}
                if(!isset($params['orderby'])) { $params['orderby'] = 'n.sticky DESC, ww.weight ASC, n.created DESC';}
                if(!isset($params['limit'])) { $params['limit'] = $this->calcPageLimit();}
                
                if($where == ''){ $where = 'TRUE'; }
                
                $countSQL = "SELECT COUNT(n.nid) $from {$params['join']} 
                             WHERE n.status = 1 AND ($where)";
                
                $this->pages = ($this->e->_db->result($countSQL) / $this->pageLength) + 1;
                
                $SQL = "$select $from {$params['join']} 
                        WHERE n.status = 1 AND ($where)
                        ORDER BY {$params['orderby']}
                        LIMIT {$params['limit']}";
                
                $nodes = $this->e->_db->query($SQL);
		
		$return = array();
		while($node = mysql_fetch_assoc($nodes)){
			$return[] = $node['nid'];
		}
		return $return;
	}
        
        public function drupal_search($keywords){
            $countSQL = 'SELECT entity_id as nid,MATCH(body_value,body_summary) AGAINST ("' . $keywords . '") as Relevance
                        FROM field_data_body
                        HAVING Relevance > 0
                        ORDER BY Relevance DESC;';
            
            $nodecount = $this->e->_db->query($countSQL);
            $this->pages = (mysql_num_rows($nodecount) / $this->pageLength) + 1;
            
            $SQL = 'SELECT entity_id as nid,MATCH(body_value,body_summary) AGAINST ("' . $keywords . '") as Relevance
                    FROM field_data_body
                    HAVING Relevance > 0
                    ORDER BY Relevance DESC
                    LIMIT ' . $this->calcPageLimit();

            $nodes = $nodes = $this->e->_db->query($SQL);
            
            $return = array();
            while($node = mysql_fetch_assoc($nodes)){
                    $return[] = $node['nid'];
            }
                        
            return $this->drupal_load_nodearray($return);
        }
	
        public function drupal_redirect($url = '',$auto = TRUE){
            if ($url == '') { $url = substr($this->e->qp(),1); }
            $redirectSQL = "SELECT IFNULL(u.alias,r.redirect) as redirect
                            FROM   redirect r 
                            LEFT OUTER JOIN url_alias u ON u.source = r.redirect
                            WHERE  r.source = '$url';";
            $redirect = $this->e->_db->result($redirectSQL);
            if (strlen($redirect) > 0){
                if ($auto){
                    $this->e->_goto($redirect,301);
                } else {
                    return $redirect;
                }
            }
        }
        
	public function drupal_load_nodes($where,$params = array()){
		$nids = $this->drupal_find($where,$params);
		if(is_array($nids)){
			return $this->drupal_load_nodearray($nids);
		} else {
			return FALSE;
		}
	}
	
	public function drupal_load_node_byURL($url){
		// $url = mysql_real_escape_string($url);
		$nid = $this->drupal_find("u.alias = '$url'");
		if(is_array($nid)){
			return $this->drupal_load_node($nid[0]);
		} else {
			return FALSE;
		}
	}
	
	public function drupal_load_nodes_byURL($url){
		// $url = mysql_real_escape_string($url);
		$nids = $this->drupal_find("u.alias LIKE '$url%'");
		if(is_array($nids)){
			return $this->drupal_load_nodearray($nids);
		} else {
			return FALSE;
		}
	}
	
	public function drupal_load_nodes_homepage($limit = 10){
		$nids = $this->drupal_find("n.promote = 1", array('limit' => $limit));
		if(is_array($nids)){
			return $this->drupal_load_nodearray($nids);
		} else {
			return FALSE;
		}
	}
	
	public function drupal_variable_get($variablename,$default = FALSE){
		$variable = $this->e->_db->query("select CONVERT(value USING utf8) from variable where name = '$variablename';");
		if($variabledata = mysql_fetch_array($variable)){
			$variablevalue = $variabledata[0];
			$variablevalue = unserialize($variablevalue);
		} else {
			$variablevalue = $default;
		}
		return $variablevalue;
	}
	
	public function drupal_book_childpages($nid){
		$childpagedata = $this->e->_db->query("select b.nid,nr.title,u.alias as url
                                                        from book b 
                                                        join node n on n.nid = b.nid 
                                                        join node_revision nr on nr.nid = n.nid and nr.vid = n.vid
                                                        left outer join url_alias u on u.source = concat('node/',n.nid)
                                                        where b.bid = $nid and b.nid <> $nid;");
		$return = array();
		while($returnpage = mysql_fetch_assoc($childpagedata)){
			$return[$returnpage['nid']] = $returnpage;
		}
		
		return $return;
	}

	public function drupal_menu_load($menuname,$parent = 0, $p1 = 'p1'){
                $SQL = "select link_title as title, IFNULL(u.alias,m.link_path) as url, m.*
                                                    from menu_links m
                                                    left outer join url_alias u on u.source = m.link_path							
                                                    where menu_name = '$menuname' and hidden = 0";
                if ($parent > 0) { 
                    $SQL .= " AND $p1 = $parent AND mlid != $parent "; 
                } else {
                    $SQL .= " and plid = 0 ";
                }
                $SQL .= " order by weight ASC";
                
		$menu = $this->e->_db->assocarray($SQL);
                
                $return = array();
                foreach ($menu as $mlid=>$menuitem){
                    $menuitem['options'] = unserialize($menuitem['options']);
                    $return[$mlid] = $menuitem;
                }
		return $return;
	}
	
}

?>