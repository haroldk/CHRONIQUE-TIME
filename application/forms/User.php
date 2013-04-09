<?php

class Application_Form_User extends Zend_Form {
	
	public  function __construct($id = null, $t = null) {

		
		parent::__construct();
		
		$this->setName('Chroniqueur');
		$this->setAction('newuser');
		
		$idH = new Zend_Form_Element_Hidden('id');
		$idH->setValue($id);
		
		$type = new Zend_Form_Element_Hidden('type');
		$type->setValue($t);
		
		$nom = new Zend_Form_Element_Text('nom');	
		$nom->setLabel('Nom : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setRequired(true)->setAttrib('placeholder', 'Nom')->setAttrib('title', 'Nom')->setAttrib('data-toggle', 'tooltip');

		$prenom = new Zend_Form_Element_Text('prenom');	
		$prenom->setLabel('Prénom : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setRequired(true)->setAttrib('placeholder', 'Prénom');
		
		$jour = new Zend_Form_Element_Select('jour');
		$jour->setLabel('Né(e) le :')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
			$j = array(); for($i=1;$i<32;$i++){ $j[$i]=$i; }
		$jour->addMultiOptions($j)->setAttrib('class', 'span1');
		
		$mois = new Zend_Form_Element_Select('mois');
		$mois->setLabel('Mois')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
			$j = array(); for($i=1;$i<13;$i++){ $j[$i]=$i; }
		$mois->addMultiOptions($j)->setAttrib('class', 'span1');
		
		$annee = new Zend_Form_Element_Select('annee');
		$annee->setLabel('Année')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
			$j = array(); for($i=date('Y');$i>date('Y')-70;$i--){ $j[$i]=$i; }
		$annee->addMultiOptions($j)->setAttrib('class', 'span2');
		
		$sexe = new Zend_Form_Element_Select('sexe');
		$sexe->setLabel('Sexe : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty');
			$j = array(); $j[]=array('key'=> 'Garçon','value'=> 'Garçon'); $j[]=array('key'=> 'Fille','value'=> 'Fille');
		$sexe->addMultiOptions($j);
		
		$pseudo = new Zend_Form_Element_Text('pseudo');	
		$pseudo->setLabel('Pseudo : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setRequired(true)->setAttrib('placeholder', 'PseudoCT');
		
		$mail = new Zend_Form_Element_Text('email');	
		$mail->setLabel('E-Mail : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setRequired(true)->setAttrib('placeholder', 'adresse@mail.ok');
		
		$mdp = new Zend_Form_Element_Password('mdp');	
		$mdp->setLabel('Mot de passe : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setRequired(true)->setAttrib('placeholder', 'Mot de passe');
		
		$mdp2 = new Zend_Form_Element_Password('mdp2');	
		$mdp2->setLabel('Conf. MDP: ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setRequired(true)->setAttrib('placeholder', 'Confirmation mdp');
		
		$tel = new Zend_Form_Element_Text('tel');	
		$tel->setLabel('Tél : ')->addFilter('StripTags')->addFilter('StringTrim')->addValidator('NotEmpty')->setAttrib('placeholder', '0605040302');
		
		$submit = new Zend_Form_Element_Submit('submit');
		$submit->setAttrib('class','btn btn-success')->setLabel('Inscrire');
		
		$this->addElements(array($idH, $type, $nom, $prenom, $jour, $mois, $annee, $sexe, $pseudo, $mail, $mdp, $mdp2, $tel, $submit));
			
		
		if( isset($id) && ($id != "" OR $id != "0") && $id != null ) {
					
	        $obj = new Application_Model_DbTable_Users();
	        $obj = $obj->fetchRow ( array ("id = ?" => $id ) );
        
	  		if( $obj != null ) {
		  		
		    	$obj = $obj->toArray ();
		    	$this->populate ( $obj );
		  		$submit->setLabel( 'MODIFIER' )->setAttrib('class', 'btn btn-warning');	  	
		  		$this->setAction('../../modif');
	
		  	}else {
		  		
		    	throw new Zend_Exception ( "Il n'éxiste pas d'utilisateur n° : " . $id );
		    	
		  	}
			  	
		}
		
	}
	
}