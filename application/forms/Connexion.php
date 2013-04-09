<?php

class Application_Form_Connexion extends Zend_Form{
	
	public function __construct($type = null){
		
		parent::__construct($type);
		
		$this->setName('Connexion');
		
		$table = new Zend_Form_Element_Hidden('type');
		$table->setValue($type);
		
		$login =  new Zend_Form_Element_Text('login');
		$login->setLabel('Identifiant :')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim');
		$login->setAttrib('placeholder','Identifiant');

		$mdp =  new Zend_Form_Element_Password('mdp');
		$mdp->setLabel('Mot de passe :')
				->setRequired(true)
				->addFilter('StripTags')
				->addFilter('StringTrim');
		$mdp->setAttrib('placeholder','password');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn')->setLabel('CONNEXION');
		
		$this->addElements( array($table,$login,$mdp,$submit) );
			
	}
}