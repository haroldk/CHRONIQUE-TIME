<?php

class Application_Form_AdminChronique extends Zend_Form{
	
	public  function __construct($id) {

		parent::__construct($id);
		$this->setName('AChronique')->setAction('newchronique');
		
		$idH = new Zend_Form_Element_Hidden('id');
		$idH->setValue($id);
		
		$theme = new Zend_Form_Element_Select('id_theme');
		$theme->setLabel('Thème : ')->addFilter('StripTags')->addFilter('StringTrim');
			$themeOpt = array();
			$themes = new Application_Model_DbTable_Themes(); 
			$select = $themes->select();
			$select->setIntegrityCheck(false)->from('themes')->order('id DESC');
			$themes = $themes->fetchAll($select); $themeOpt[] = array('key'=> '0','value'=> 'Choisissez un thème');
			foreach ($themes as $c){	$themeOpt[] = array('key'=> $c->id,'value'=> $c->libele); 	}
		$theme->addMultiOptions($themeOpt);
		
		$chroniqueur = new Zend_Form_Element_Select('id_chroniqueur');
		$chroniqueur->setLabel('Chroniqueur : ')->addFilter('StripTags')->addFilter('StringTrim');
			$chOpt = array();
			$ch = new Application_Model_DbTable_Users(); 
			$select = $ch->select();
			$select->setIntegrityCheck(false)->from('users')->where('chroniqueur = ?',1)->order('id ASC');
			$ch = $ch->fetchAll($select); $chOpt[] = array('key'=> '0','value'=> "Choisissez l'éditeur");
			foreach ($ch as $c){	$chOpt[] = array('key'=> $c->id,'value'=> $c->pseudo); 	}
		$chroniqueur->addMultiOptions($chOpt);

		$titre = new Zend_Form_Element_Text('titre');	
		$titre->setLabel('Titre : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-primary')->setLabel('Poster');
		
		$this->addElements(array($idH, $titre, $theme,$chroniqueur, $submit));
			
		if( isset($id) && ($id != "" OR $id != "0") ) {
					
	        $obj = new Application_Model_DbTable_Chroniques();
	        $obj = $obj->fetchRow ( array ("id = ?" => $id ) );
        
	  		if( $obj != null ) {
		  		
		    	$obj = $obj->toArray ();
		    	$this->populate ( $obj );
		  		$submit->setLabel( 'MODIFIER' )->setAttrib('class', 'btn btn-warning');	  	
		  		$this->setAction('../../modif');
	
		  	}else {
		  		
		    	throw new Zend_Exception ( "Il n' éxiste pas d'élément n° : " . $id );
		    	
		  	}
			  	
		}
	}
}