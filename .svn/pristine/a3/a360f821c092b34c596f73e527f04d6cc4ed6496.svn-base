<?php

class Application_Form_Mail extends Zend_Form {
	
	public  function __construct() {

		
		parent::__construct();
		$this->setName('Mail');
		
		$mail = new Zend_Form_Element_Text('email');	
		$mail->setLabel('E-Mail : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
		
		$objet = new Zend_Form_Element_Text('objet');	
		$objet->setLabel('Objet : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
		
		$text = new Zend_Form_Element_Textarea('texte',array('rows' => 12, 'cols' => 15));	
		$text->setLabel('Message : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');	
		
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-success')->setLabel('Envoyer');
		
		$this->addElements( array($mail, $objet, $text, $submit) );
			
		
	}
	
}