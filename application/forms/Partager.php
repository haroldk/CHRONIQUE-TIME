<?php

class Application_Form_Partager extends Zend_Form {
	
	public  function __construct($id = null) {

		
		parent::__construct($id = null);
		$this->setName('Partager');
		
		$idH = new Zend_Form_Element_Hidden('id_partage');
		$idH->setValue($id);
		
		$mail = new Zend_Form_Element_Text('mail');	
		$mail->setLabel(' ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setAttrib('placeholder','kronic@time.fr, deuxieme@email.com');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-success')->setLabel('Envoyer');
		
		$this->addElements( array($idH, $mail, $submit) );
			
		
	}
	
}