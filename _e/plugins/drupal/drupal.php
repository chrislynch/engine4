<?php

class _drupal{

	public function __construct(&$e){
		$this->e =& $e;
		$e->_loadPlugin('db');
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
					$select .= ",f$i.{$field->field_name}_value";
					$from .= " join field_data_{$field->field_name} f$i ON f$i.entity_id = n.nid ";
				}
				$i++;
				
			}
			
			$node = $this->e->_db->query("$select $from WHERE n.nid = $nid");
			$node = mysql_fetch_assoc($node);
			
			return $node;
		
		}
		
	}
	
	private function drupal_find($where){
		$select = "SELECT n.*, u.alias as url ";
		$from = " from url_alias u
				  left outer join node n on n.nid = reverse(substring_index(reverse(u.source),'/',1)) ";
		
		$nodes = $this->e->_db->query("$select $from WHERE $where");
		
		$return = array();
		while($node = mysql_fetch_array($nodes)){
			$return[] = $node['nid'];
		}
		return $return;
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