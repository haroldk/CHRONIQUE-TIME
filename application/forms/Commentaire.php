<?php

class Application_Form_Commentaire extends Zend_Form{
	
	public function __construct($options = null){
		parent::__construct($options);
		
		$this->setName('Commentaire');
		
		$page = new Zend_Form_Element_Select('page');
		$page->setLabel('Page à commenter :')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
			$j = array(); 
			for( $i=1; $i<=$options; $i++ ){ $j[$i]=$i; }
		$page->setAttrib('class','span1');
		$page->addMultiOptions($j);
		
		$commentaire = new Zend_Form_Element_Textarea('commentaire');
		$commentaire->setLabel('Commentaire :')
		->setRequired(true)
		->addFilter('StripTags')
		->addFilter('StringTrim');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-primary')->setLabel('Poster');
		
		$this->addElements(array($page, $commentaire, $submit));
			
	}
}