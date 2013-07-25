<?php

class _drupal{

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
				  left outer join node n on n.nid = reverse(substring_index(reverse(u.source),'/',1)) ";
		$i = 1;
				
		$node = $this->e->_db->query("SELECT * FROM node WHERE nid = $nid");
		
		if ($node = mysql_fetch_assoc($node)){
			$fields = $this->e->_db->query("select fci.field_name,fc.module
					from field_config_instance fci
					join field_config fc on fc.id = fci.field_id
					where bundle = '{$node['type']}'");
			
			while($field = mysql_fetch_object($fields)){
				if($field->module == 'text'){
					$select .= ",f$i.{$field->field_name}_value, f$i.{$field->field_name}_summary";
					$from .= " LEFT OUTER join field_data_{$field->field_name} f$i ON f$i.entity_id = n.nid ";
				}
				if($field->module == 'file'){
					$select .= ",GROUP_CONCAT(fm$i.filename,'|') as {$field->field_name}_filename";
					$from .= " LEFT OUTER join field_data_{$field->field_name} f$i ON f$i.entity_id = n.nid 
							   LEFT OUTER join file_managed fm$i ON fm$i.fid = f$i.{$field->field_name}_fid";
				}
				$i++;
				
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
	
	private function drupal_find($where = '', $params = array()){
		$select = "SELECT n.*, u.alias as url ";
		$from = " from url_alias u
				  left outer join node n on n.nid = reverse(substring_index(reverse(u.source),'/',1)) ";
		                        
                if(!isset($params['join'])) { $params['join'] = '';}
                if(!isset($params['orderby'])) { $params['orderby'] = 'n.sticky DESC, n.created DESC';}
                if(!isset($params['limit'])) { $params['limit'] = 10;}
                
                if($where == ''){ $where = 'TRUE'; }
                
                $SQL = "$select $from {$params['join']} 
                                                WHERE n.status = 1 AND ($where)
                                                ORDER BY {$params['orderby']} 
                                                LIMIT {$params['limit']}";
                
                $nodes = $this->e->_db->query($SQL);
		
		$return = array();
		while($node = mysql_fetch_array($nodes)){
			$return[] = $node['nid'];
		}
		return $return;
	}
	
	public function drupal_load_nodes($where,$params = array()){
		$nids = $this->drupal_find($where,$params);
		if(is_array($nids)){
			$return = array();
			foreach($nids as $nid){
				$node = $this->drupal_load_node($nid);
				$return[$node['nid']] = $node;
			}
			return $return;
		} else {
			return FALSE;
		}
	}
	
	public function drupal_load_node_byURL($url){
		$nid = $this->drupal_find("u.alias = '$url'");
		if(is_array($nid)){
			return $this->drupal_load_node($nid[0]);
		} else {
			return FALSE;
		}
	}
	
	public function drupal_load_nodes_byURL($url){
		$nids = $this->drupal_find("u.alias LIKE '$url%'");
		if(is_array($nids)){
			$return = array();
			foreach($nids as $nid){
				$node = $this->drupal_load_node($nid);
				$return[$node['nid']] = $node;
			}
			return $return;
		} else {
			return FALSE;
		}
	}
	
	
}

?>