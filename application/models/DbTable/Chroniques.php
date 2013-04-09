<?php
class Application_Model_DbTable_Chroniques extends Zend_Db_Table_Abstract{

	protected $_name = 'chroniques';
	protected $_primary = 'id';
	
	public function All($order){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select();
		$select->setIntgrityCheck(false)
				->from('chroniques')
				->order('id '.$order);
		return $db->fetchAll($select);
	}
	
	
}