<?php

class Application_Form_AdminRecherche extends Zend_Form{
	
	public  function __construct($id) {

		
		parent::__construct($id);
		
		$this->setName('AChronique');
		$this->setAction('search');
		
		$idH = new Zend_Form_Element_Hidden('id');
		$idH->setValue($id);
		
		$titre = new Zend_Form_Element_Text('search');	
		$titre->setLabel('Recherche : ');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-info')->setLabel('Rechercher');
		
		$this->addElements(array($idH, $titre, $submit));
			
			  	
	}
	
}