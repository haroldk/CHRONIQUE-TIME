<?php
class AjaxController extends Zend_Controller_Action{
	
 	public function init(){
 		
 	  $this->_helper->layout->setLayout('blank');
 	  
    }
	
	public function recommandationAction(){
		
		
     	$auth = Zend_Auth::getInstance();
		$id_user = $auth->getIdentity()->id;
		$pseudo = $auth->getIdentity()->pseudo;
     	$id_chro = $this->_getParam('id_chronique');
     	
     	$recommandations = new Application_Model_DbTable_Recommandations();
		$recommandation = $recommandations->fetchRow( array ("id_user = ?" => $id_user,"id_chronique = ?" => $id_chro) );
		if(!$recommandation){
			
			$recommandations = new Application_Model_DbTable_Recommandations();
			$row = $recommandations->createRow();
			$row->id_user = $id_user;
			$row->id_chronique = $id_chro;
			
			if($row->save()){
			
				$chroniques = new Application_Model_DbTable_Chroniques();
				$chronique = $chroniques->fetchRow( array ("id = ?" => $id_chro) );
				
				$data = array();
				$where = " id = $id_chro ";
				$data['login_recom'] = $pseudo;
				$data['recommendations'] = $chronique->recommendations + 1;
				
				$chroniques->update($data, $where);
			
			}
		}
		
	}
	
	
	public function commentairesAction(){
		
		$id_chro = $this->_getParam('id_chronique');
		$num_page = $this->_getParam('num_page');
		
		$commentaires = new Application_Model_DbTable_Commentaires();
		$select = $commentaires->select();
		$select->setIntegrityCheck(false)
				->from(array('c'=>'commentaires'), array('c.id as id','c.id_user as user','c.id_chronique as id_k','c.id_page as page','c.commentaire as comm','c.pseudo as pseudo','c.email as email','c.date_publication as publication','c.signal_abus as abus','c.valid as valid'))
				->where('c.id_chronique = '.$id_chro)
				->where('c.id_page = '.$num_page)
				->where('c.valid = 1')
				->joinLeft(array('k'=>'chroniques'), 'c.id_chronique = k.id', array('k.titre as chronique'))
				->joinLeft(array('p'=>'chroniques_pages'), 'c.id_page = p.id', array('p.texte as texte')); 
		$commentaires = $commentaires->fetchAll($select);
		$this->view->commentaires = $commentaires;
		
		
	}
	

	public function filtrerAction(){
		
		
		$id_theme = $this->_getParam('id_theme');
		$tri = $this->_getParam('tri');
		
		if($tri == "Date"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('c.date_publication ASC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Date_2"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('c.date_publication DESC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Note"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('c.note DESC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Note_2"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('c.note ASC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Vue"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('c.nb_vue DESC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Vue_2"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('c.nb_vue ASC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Comm"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('commentaires DESC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}elseif($tri == "Comm_2"){
			
			//chroniques du theme
			$chroniques = new Application_Model_DbTable_Chroniques();
			$select = $chroniques->select();
			$select->setIntegrityCheck(false)
					->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
					->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
					->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
					->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
					->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
					->joinLeft(array('x'=>'chroniques_pages'), 'c.id = x.id_chronique AND x.page = 1', array('x.texte as texte'))
					->where('c.valid = 1')
					->where('c.id_theme = ?',$id_theme)
					->order('commentaires ASC')
					->group(array('c.id','p.id_chronique','q.id_chronique'));
			$chroniques = $chroniques->fetchAll($select);
			$this->view->chroniques = $chroniques;
			
		}
	}
	
	
	
	
	
	
}
