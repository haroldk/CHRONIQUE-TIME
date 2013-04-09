<?php

class Formulaire extends Zend_Form{
	
	public function __construct($options = null){
		
	parent::__construct($options);
	$this->setName('Formulaire');
	$this->setAttrib('enctype', 'multipart/form-data');
	
	
	$id = new Zend_Form_Element_Hidden('id');
	
	//------------------------------------------------
	//		PARTIE DEMANDE
	//------------------------------------------------
	
	$email = new Zend_Form_Element_Text('email');
	$email->setLabel('email')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('NotEmpty');
	
	$nom = new Zend_Form_Element_Text('nom');
	$nom->setLabel('Nom')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('NotEmpty');
	
	$tel = new  Zend_Form_Element_Text('tel');
	$tel->setLabel('telephone')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('NotEmpty');
	
	$postal = new Zend_Form_Element_Text('postal');
	$postal->setLabel('Code Postal')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('NotEmpty');
	
//	$vous = new Zend_Form_Element('vous');
//	$vous->setLabel('Vous')
//	->setRequired(true)
//	->addFilter('StripTags')
//	->addFilter('StringTrim')
//	->addValidator('NotEmpty');
	
	$vous = new Zend_Form_Element_Radio('vous');
    $vous->setLabel('Vous')
   	->setRequired(true)// OBLIGER DE METTRE SA DANS TOUT LES TRUK
   	->addValidator('NotEmpty', true)
    ->addMultiOptions(array('Particulier' => 'Particulier','Professionel' => 'Professionel'));
    
	
	$pass=new Zend_Form_Element_Password('pass');
    $pass->setLabel('Mot de passe:');
    $pass->setRequired(true)->addErrorMessage("champ requis");
    
    //---------------------------------------------------------------------------------------------
    //						 PARTIE ANNONCE
    //---------------------------------------------------------------------------------------------
    
    $file = new Zend_Form_Element_File('file');
    $file->setLabel('Telecharger une photo')
    ->addValidator('Extension', false, 'jpg,png,gif')// seulement des JPEG, PNG, et GIFs
	->setDestination('upload')// je vais envoyé tout mes fichier dans le dossier upload
	->setRequired(true);
	
	$etat = new Zend_Form_Element_Radio('etat');
    $etat->setLabel('Etat')
   	->setRequired(true)// OBLIGER DE METTRE SA DANS TOUT LES TRUK
   	->addValidator('NotEmpty', true)
    ->addMultiOptions(array('Neuf' => 'Neuf','Occasion' => 'Occasion','Autre'=>'Autre' ))

      //->separator ... ser a aligné sur une ligne les bouton radio
      ->setSeparator('');
	
      
    $validatorTitre = new Zend_Validate_StringLength(array('min' => 10, 'max' => 255));
    $validatorTitre->setMessages( array(
        Zend_Validate_StringLength::TOO_SHORT =>
            'Le titre est trop court',
    Zend_Validate_StringLength::TOO_LONG  =>
            'Le titre est trop longue'
    ));
	
	$titre = new Zend_Form_Element_Text('titre');
	$titre->setLabel('Titre')
	
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator($validatorTitre)
	->setRequired(true);
	

	$description = new Zend_Form_Element_Textarea('description');
	$description->setLabel('description')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('NotEmpty');
	
	
	
	$prix = new  Zend_Form_Element_Text('prix');
	$prix->setLabel('prix')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('regex', false, array('/^[0-9]/i'))// require des chiffre entre 0 et 9 c'st tt	
	->addValidator('NotEmpty');
       
	//------------------------------------------------------------------------------------------------------
	//		PARTIE PORTABLE
	//------------------------------------------------------------------------------------------------------
    
	$marque = new Zend_Form_Element('marque');
	$marque->setLabel('Marque')
	->setRequired(true)
	->addFilter('StripTags')
	->addFilter('StringTrim')
	->addValidator('NotEmpty');

	
	$operateur = new Zend_Form_Element_Select('operateur');
    $operateur->setLabel('operateur')
    ->setMultiOptions(array('Orange'=>'Orange', 'Sfr'=>'Sfr', 'Bouygue'=>'Bouygue', 'Debloque'=>'Debloque'))
    ->setRequired(true)
    ->addValidator('NotEmpty', true);
	
	
	
	//multichekbox c'est les carré ou on po en séléctioné plusieure
//	$group = new Zend_Form_Element_MultiCheckbox('groups');
//	$group->addMultiOption(1,'A');
//	$group->addMultiOption(2,'B');
//	$group->addMultiOption(3,'C');


//      //Fait en sorte qu'il y ait un seul fichier
//      $element->addValidator('Count', false, 1);
//      // limite à 100K
//      $element->addValidator('Size', false, 102400);

	
	
	$submit = new Zend_Form_Element_Submit('submit');
    $submit->setLabel('Ajouter');
    
   
	
//	$this->addElements(array($id,$email,$nom,$tel,$postale,$vous,$pass,$file,$etat,$titre,$description,$prix,$marque,$operateur, $submit));
	$this->addElements(array($id,$email,$nom,$tel,$postal,$vous,$pass,$file,$etat,$prix,$marque,$operateur,$titre,$description,$submit));
	}
}