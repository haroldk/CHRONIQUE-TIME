<?php
class Application_Model_DbTable_Users extends Zend_Db_Table_Abstract{

	protected $_name = 'users';
	protected $_primary = 'id';
	
	
	public function All($order,$type){
		$db = Zend_Db_Table::getDefaultAdapter();
		$select = $db->select();
		$select->setIntgrityCheck(false)
				->from('users')
				->where('chroniqueur = ?', $type)
				->order('id '.$order);
		return $db->fetchAll($select);
	}
	
	
}

/*
	public function UserExist($mail){
		$sql = 'Select count(*) as compteur FROM users where email="'.trim($mail).'"';
		$nb = $this->_db->fetchRow($sql);
		return $nb['compteur'];
	}
	
	public function Inscription($mail,$password,$name){
		if(!$this->UserExist($mail)){
			$sql ="INSERT INTO users (email, password, name,active)
						 VALUES ('".$mail."', '".md5($password)."', '".$name."',0)";
			$this->_db->query($sql);
		}
	}
	
	public function ModifCompte($data,$id){
		$db = Zend_Db_Table::getDefaultAdapter();
    $db->update('users', $data, 'id ='.$id);
	}
	
}

*/