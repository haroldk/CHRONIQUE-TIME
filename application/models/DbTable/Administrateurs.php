<?php
	class Application_Model_DbTable_Administrateurs extends Zend_Db_Table_Abstract{
	
		protected $_name = 'administrateurs';
		protected $_primary = 'id';
		
		public function All($order){
			$db = Zend_Db_Table::getDefaultAdapter();
			$select = $db->select();
			$select->setIntgrityCheck(false)
					->from('chroniqueurs')
					->order('id '.$order);
			return $db->fetchAll($select);
		}
		
		
	}