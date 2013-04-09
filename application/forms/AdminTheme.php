<?php

class Application_Form_AdminTheme extends Zend_Form{
	
	public  function __construct($id, $action=null) {

		parent::__construct($id, $action);
		$this->setName('ATheme')->setAction('newtheme');
		
		$idH = new Zend_Form_Element_Hidden('id');
		$idH->setValue($id);
		
		$titre = new Zend_Form_Element_Text('libele');	
		$titre->setLabel('Libele : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
					
				
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-primary')->setLabel('Ajouter');
		
		$this->addElements(array($idH, $titre, $submit));
		
		
		if( isset($id) && ($id != "" OR $id != "0") && $id != null) {
					
	        $obj = new Application_Model_DbTable_Themes();
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