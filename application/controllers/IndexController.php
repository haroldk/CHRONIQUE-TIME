<?php
class IndexController extends Zend_Controller_Action {
	
	
	function preDispatch() {
		
		
	}
	
	
    public function init() {
    	
    	
    	$this->initView();
    	$this->view->baseUrl = $this->_request->getBaseUrl();
    	
    	//form connexion
		$connexion = new Application_Form_Connexion();
		$this->view->connexion = $connexion;
		
		
		//controle authentification
		$auth = Zend_Auth::getInstance();
		$ok = $auth->hasIdentity();
		if(isset($_SESSION['Zend_Auth']['storage']->chroniqueur)) {
			
			($auth->getIdentity()->chroniqueur == 1 ? $type='chroniqueur' : $type='lecteur');
			$this->view->pseudo = $auth->getIdentity()->pseudo;
			
	  		//btn-compte
	  		$this->view->compte = '
	  			<div id="btn-compte">
					<span>
						<a href="'.$this->_request->getBaseUrl().'/'.$type."/compte".'" title="mon compte">
							<img src="'.$this->_request->getBaseUrl()."/img/comptes/icon_".$auth->getIdentity()->sexe.".png".'" alt="mon compte" class="avatar" width="20" />
						</a>
					</span>
					<span>
						<a href="'.$this->_request->getBaseUrl().'/'.$type."/compte".'" title="mon compte">'.
	  						$auth->getIdentity()->pseudo.'
	  					</a>
	  				</span>
					<span>
						<a href="'.$this->_request->getBaseUrl().'/auth/logout" title="déconnexion">
	  						<img src="'.$this->_request->getBaseUrl()."/img/comptes/unlock.png".'" alt="mon compte" class="logout" width="24" />
						</a>
	  				</span>
				</div>
			';
	  		
		}else{
			
			$this->view->compte = '	
				<div id="btn-connexion">
					<img alt="connexion chronique time" src="'.$this->_request->getBaseUrl().'/img/general/icon_plus.png" />
					CONNEXION
				</div>
			';
			
		}
		
		if ($this->_request->isPost('mdp')) {
			
			// collect the data from the user
			$f = new Zend_Filter_StripTags();
			$login = $f->filter($this->_request->getPost('login'));
			$mdp = $f->filter($this->_request->getPost('mdp'));
			$mdp = md5($mdp);
					

			if (empty($login)) {
				
				$this->view->message = '<div class="alert alert-error">Veuillez entrer votre mail.</div>';
				
			}else{
				 
				$dbAdapter = Zend_Db_Table::getDefaultAdapter();
	
				$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
				$authAdapter->setTableName('users'); 
				$authAdapter->setIdentityColumn('email');
				$authAdapter->setCredentialColumn('mdp');
	
				$authAdapter->setIdentity($login);
				$authAdapter->setCredential($mdp);
	
				$auth = Zend_Auth::getInstance();
				$result = $auth->authenticate($authAdapter);

				if ($result->isValid()) {
					$data = $authAdapter->getResultRowObject(null, 'password');
					$auth->getStorage()->write($data);
					$auth = Zend_Auth::getInstance();
					$statut = $auth->getIdentity()->valid;
					
					
					if($statut == 1) { 
						
						//inserer date dern connexion
						$users = new Application_Model_DbTable_Users();
						
						$data = array();
						$data['date_connexion'] = date('Y-m-d H:i:s');
						$where  = " id = ".$auth->getIdentity()->id;
							
						if( $users->update($data, $where) ){
							
							echo '<script type="text/javascript">
							<!--
								window.location.reload(true); 
							//-->
							</script>';

						}
					
					}elseif($statut == 0) {
					
						// banned: clear database row from session
						$this->view->message = "<br/><div class='alert alert-error acenter'>Votre compte a été banni !<br/>Pour tout complément d'information veuillez transmettre votre requête à l'adresse mail suivante : contact@chroniquetime.fr</div>";
						Zend_Auth::getInstance()->clearIdentity();
						
					}else {
					
						// failure: clear database row from session
						$this->view->message = "<br/><div class='alert alert-error'>Échec de l'identification.</div>";
						
					} 
	
				} else {
			
					// failure: clear database row from session
					$this->view->message = "<br/><div class='alert alert-error'>Échec de l'identification.</div>";
				}
				
			}
		}
	
    }

    
	function indexAction(){
				
		
		//dernieres chroniques
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
				->limit('4')
				->order('c.id DESC')
				->group(array('c.id','p.id_chronique','q.id_chronique')); 
		$chroniques = $chroniques->fetchAll($select);
		$this->view->dernieres = $chroniques;
		
		
		//top5 vues chroniques
		$chroniques = new Application_Model_DbTable_Chroniques();
		$select = $chroniques->select();
		$select->setIntegrityCheck(false)
				->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
				->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
				->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
				->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
				->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
				->where('c.valid = 1')
				->limit('5')
				->order('c.nb_vue DESC')
				->group(array('c.id','p.id_chronique','q.id_chronique')); 
		$chroniques = $chroniques->fetchAll($select);
		$this->view->top5 = $chroniques;
	
		//recommendations chroniques
		$chroniques = new Application_Model_DbTable_Chroniques();
		$select = $chroniques->select();
		$select->setIntegrityCheck(false)
				->from( array('c'=>'chroniques'), array('c.id as id','c.titre as titre','c.recommendations as reco','c.nb_vue as vue','c.login_recom as login') )
				->where('c.valid = 1')
				->limit('5')
				->order('c.recommendations DESC'); 
		$chroniques = $chroniques->fetchAll($select);
		$this->view->reco = $chroniques;
		
		//themes aléatoires
		$themes = new Application_Model_DbTable_Themes();
		$select = $themes->select();
		$select->setIntegrityCheck(false)
				->from('themes')
				->limit('4')
				->order('rand()');
		$themes = $themes->fetchAll($select);
		$this->view->themes = $themes;
		
		
		//inscription ok
		$ok = $this->_getParam('register');
		if(!empty($ok) && $ok == "1" ){
			echo '<script type="text/javascript">
 					$(function() {
					   $( "#register-ok" ).dialog({
					      resizable: false,
					      width: 310,
					      height:140,
					      modal: true
					   });
					});
				 </script>
				';
		}
		//validation ok
		$ok = $this->_getParam('register');
		if(!empty($ok) && $ok == "2" ){
			echo '<script type="text/javascript">
 					$(function() {
					   $( "#valid-ok" ).dialog({
					      resizable: false,
					      width: 230,
					      height:130,
					      modal: true
					   });
					});
				 </script>
				';
		}
		
		
	}

	function chroniqueAction(){
		
		//LA chroniques
		$id = $this->_getParam('id');
		$chroniques = new Application_Model_DbTable_Chroniques();
		$select = $chroniques->select();
		$select->setIntegrityCheck(false)
				->from(array('c'=>'chroniques'), array('c.id as num','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid','c.id_theme as id_theme'))
				->joinLeft(array('t'=>'themes'), 'c.id_theme = t.id', array('t.libele as theme'))
				->joinLeft(array('k'=>'users'), 'c.id_chroniqueur = k.id', array('k.pseudo as chroniqueur'))
				->joinLeft(array('q'=>'commentaires'), 'c.id = q.id_chronique', array('COUNT(DISTINCT q.id) as commentaires'))
				->joinLeft(array('p'=>'chroniques_pages'), 'c.id = p.id_chronique', array('COUNT(DISTINCT p.id) as pages'))
				->where('c.id = '.$id)
				->where('c.valid = 1')
				->limit('1'); 
		$chronique = $chroniques->fetchRow($select);
		$this->view->chronique = $chronique;
		
		$ch_pages = new Application_Model_DbTable_Chroniquespages();
		$select = $ch_pages->select();
		$select->setIntegrityCheck(false)
				->from(array('p'=>'chroniques_pages'), array('DISTINCT(p.id) as id_page','p.id_chronique as chronique','p.page as page','p.texte as texte'))
				->joinLeft(array('q'=>'commentaires'), 'q.id_chronique = '.$id.' AND p.id = q.id_page', array('q.pseudo as pseudo','q.commentaire as com','q.date_publication as date'))
				->where('p.id_chronique = '.$id)
				->order('p.page ASC');
		$ch_pages = $ch_pages->fetchAll($select);
		$this->view->pages = $ch_pages;
		
		$commentaires = new Application_Model_DbTable_Commentaires();
		$select = $commentaires->select();
		$select->setIntegrityCheck(false)
				->from(array('c'=>'commentaires'), array('c.id as id','c.id_user as user','c.id_chronique as id_k','c.id_page as page','c.commentaire as comm','c.pseudo as pseudo','c.email as email','c.date_publication as publication','c.signal_abus as abus','c.valid as valid'))
				->where('c.id_page = 1')
				->where('c.id_chronique = '.$chronique->num)
				->where('c.valid = 1')
				->joinLeft(array('k'=>'chroniques'), 'c.id_chronique = k.id', array('k.titre as chronique'))
				->joinLeft(array('p'=>'chroniques_pages'), 'c.id_page = p.id', array('p.texte as texte')); 
		$commentaires = $commentaires->fetchAll($select);
		$this->view->commentaires = $commentaires;
		
		//themes aléatoires
		$themes = new Application_Model_DbTable_Themes();
		$select = $themes->select();
		$select->setIntegrityCheck(false)
				->from('themes')
				->limit('4')
				->order('rand()');
		$themes = $themes->fetchAll($select);
		$this->view->themes = $themes;
		
		//chroniques similaire
		$chroniques = new Application_Model_DbTable_Chroniques();
		$select = $chroniques->select();
		$select->setIntegrityCheck(false)
				->from(array('c'=>'chroniques'), array('c.id as id','c.titre as titre','c.date_publication as date','c.note as note','c.signal_abus as abus','c.nb_vue as visites','c.valid as valid'))
				->where('c.valid = 1')
				->where('c.id_theme = '.$chronique->id_theme)
				->limit('4')
				->order('rand()');
		$chroniques = $chroniques->fetchAll($select);
		$this->view->similaires = $chroniques;
		
		
		//formulaire commentaire
		$nb_pages = 0;
		foreach ($ch_pages as $page){ $nb_pages++; }
		$NewCommentaire = new Application_Form_Commentaire($nb_pages);	
		$this->view->newCom = $NewCommentaire;	
		//post commentaire
		if ($this->_request->isPost('page') && $this->_request->isPost('commentaire') && $this->_request->getPost('commentaire') != null && $this->_request->getPost('commentaire') != "") {

			$auth = Zend_Auth::getInstance();
			$f = new Zend_Filter_StripTags();
			$formData = $this->_request->getPost();
			$form = new Application_Form_Commentaire();
			$id_page = $f->filter($this->_request->getPost('page'));
			$commentaire = $f->filter($this->_request->getPost('commentaire'));
			$pseudo = $auth->getIdentity()->pseudo;
			$email = $auth->getIdentity()->email;
			$id_user = $auth->getIdentity()->id;
			
			$bddcom = new Application_Model_DbTable_Commentaires();
			$row = $bddcom->createRow();
			$row->id_chronique = $id;
			$row->id_page = $id_page;
			$row->commentaire = $commentaire;
			$row->pseudo = $pseudo;
			$row->email = $email;
			$row->valid = 1;
			$row->id_user = $id_user;
			
			if($row->save()){
				
				echo '<script type="text/javascript">
 					$(function() {
					   $( "#CmOk" ).dialog({
					      resizable: false,
					      width: 250,
					      height:160,
					      modal: true,
					   });
					   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
					});
				 </script>
				';
			}
			
			
		}
		
		
		//formulaire partage
		$partager = new Application_Form_Partager($chronique->num);
		$this->view->form_partager = $partager;
		//partage chronique
		if ($this->_request->isPost('id_partage') && $this->_request->isPost('mail') && $this->_request->getPost('mail') != null && $this->_request->getPost('mail') != "") {

			$f = new Zend_Filter_StripTags();
			$formData = $this->_request->getPost();
			$form = new Application_Form_Partager();
			//$validateur = new Zend_Validate_EmailAddress();
			$id = $f->filter($this->_request->getPost('id_partage'));
			$mail = $f->filter($this->_request->getPost('mail'));

			
			/*if (!$validateur->isValid($mail) && $mail!="" ) { $this->view->message = '<div class="alert alert-error">Votre adresse mail n\'est pas valide</div>'; }
			else*/if($form->isValid($formData)) {
				
				//if( filter_var($mail, FILTER_VALIDATE_EMAIL) ){

					$bddemails = new Application_Model_DbTable_Email();
					$select = $bddemails->select();
					$select->setIntegrityCheck(false)
							->from('email')
							->where('email = ?',$mail);
					$bddemails = $bddemails->fetchRow($select);
					if(!$bddemails){
						
						$bddemails = new Application_Model_DbTable_Email();
						$row = $bddemails->createRow();
						$row->id_chronique = $chronique->num;
						$row->email = $f->filter($this->_request->getPost('mail'));
						$row->save();
					}
					
					$auth = Zend_Auth::getInstance();
					($auth->hasIdentity() ? $prenom = $auth->getIdentity()->prenom : $prenom = "Un ami");
					($auth->hasIdentity() ? $from = $auth->getIdentity()->email : $from = "votre@mis.com");
					
					$ch_pages = new Application_Model_DbTable_Chroniquespages();
					$select = $ch_pages->select();
					$select->setIntegrityCheck(false)
							->from(array('p'=>'chroniques_pages'), array('p.texte as texte'))
							->where('p.id_chronique = '.$chronique->num)
							->where('p.page = 1');
					$ch_pages = $ch_pages->fetchRow($select);

					$msg = "<html>
								<head> <meta http-equiv='Content-Type' content='text/html; charset='utf-8' /> </head>
								<body>
									<H2> Bonjour !</H2> 
									
									<br/> $prenom souhaite vous faire partager une chronique en particulier sur <a href='http://chroniquetime.fr/public/index' title='chronique time'>chroniquetime.fr</a> :
									<br /><br />
									<br />
										<b><i>$chronique->titre</i></b>
									<br />
									<br />
										<i>".$ch_pages->texte."</i>
									<br />
										...
									<br />
									<br />
									<br />
										Vous pouvez retrouver l'int&eacute;gralit&eacute; de cette chronique sur le site internet d&eacute;di&eacute; aux chroniques <a href='http://chroniquetime.fr/public/index/chronique/id/".$chronique->num."' title='chronique time'>www.chroniquetime.fr</a> <br/>
										vous y retrouverez encore bien d'autres chroniques plus int&eacute;r&eacute;ssenntes les unes que les autres. 
									<br />
									<br /> 
										&Agrave; bientot sur chroniquetime.
									<br />
									<br />
										 Cordialement, <br />
										 L'&eacute;quipe de ChroniqueTime <br />
									<br />
										  &nbsp;&nbsp;&nbsp;<a href='http://www.baildy.fr' title='Baildy Group'><img src='http://www.baildy.fr/public/img/logo.png' alt='baildy group' width='255'></a>
									<br />
									<br />
									<br />
										 ------- Ne pas r&eacute;pondre &agrave; ce mail -------
								</body>
							</html>
					";
					
					$mail = new Zend_Mail();
					$mail->setBodyText($msg);
					$mail->setBodyHtml($msg);
					$mail->setFrom($from, $prenom);
					$mail->addTo('"'.$f->filter($this->_request->getPost('mail')).'"');
					$mail->setSubject('Lis celle-la !!');
					if($mail->send()){
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#partage-ok" ).dialog({
								      resizable: false,
								      width: 270,
								      height:170,
								      modal: true,
								      buttons: { Ok : function() { $( this ).dialog( "close" ); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script>
							';
					}	
					$form->reset();
					
				/*}else{
						
					$this->view->msg = "<div class='alert alert-error'>Erreur de mail, veuillez saisir une adresse mail valide.</div>";
				    foreach ($validateur->getMessages() as $message) { echo "$message\n"; }
			    
				}*/
				
			}else{
				
				$this->view->message = '<div class="alert alert-error">Veuillez remplir tous les champs</div>';
				if( $form->getValue('email') ) $form->getElement('email')->setValue($form->getValue('email'));
				
			} 
			
				
		}  // end partage chronique
		
		
		//post abus
		if ($this->_request->isPost('motif-abus') && $this->_request->getPost('id-abus') != null && $this->_request->getPost('id-abus') != "") {
		
			$f = new Zend_Filter_StripTags();
			$formData = $this->_request->getPost();
			$id = $f->filter($this->_request->getPost('id-abus'));
			$motif = $f->filter($this->_request->getPost('motif-abus'));
			$auth = Zend_Auth::getInstance();
			$id_user = $auth->getIdentity()->id;
			
			$abus = new Application_Model_DbTable_Abus();
			$row = $abus->createRow();
			$select = $abus->select();
			$select->setIntegrityCheck(false)
					->from('abus')
					->where('id_user = ?', $id_user)
					->where('id_chronique = ?', $id);
			$abus = $abus->fetchRow($select);
			
			if(!isset($abus->motif)) {
				
				$row->id_user = $id_user;
				$row->id_chronique = $id;
				$row->motif = $motif;
				
				$chroniques = new Application_Model_DbTable_Chroniques();
				$chronique = $chroniques->find($id)->current();
				$chronique->signal_abus = $chronique->signal_abus + 1; 
				$chronique_name = $chronique->titre;
				
				if($row->save() && $chronique->save()) {
					
					echo '<script type="text/javascript">
		 					$(function() {
							   $( "#signaler-ok" ).removeClass("none");
							});
						 </script> ';
					
					$conteneur='<html>
									<body>
										Cher administrateur,<br/><br/>
										Un menbre de chronique-time vient de signaler une chronique !<br /><br />
										<H4>'.$chronique_name.'</H4> <p>'.$motif.'</p> <br />
										Veuillez bien suivre cette chronique afin quel ne porte pas atteinte &eacute; nos lecteur.<br />
										<a href="http://chroniquetime.fr">CHRONIQUE-TIME</a><br/>
										Cordialement,<br/><br/>
										L\' &eacute;quipe Baildy<br/>
									</body>
								</html>';
					
					$mail = new Zend_Mail();
					$mail->setBodyHtml($conteneur);
					$mail->setFrom('abus@chroniquetime.fr', 'Chronique T.');
					$mail->addTo('harold.kiusi@gmail.com', 'Admin');
					$mail->setSubject('Abus ChroniqueTime');
					$mail->send();
					
				} 
				
			}else{
				
				echo '<script type="text/javascript">
	 					$(function() {
						   $( "#signaler-ok" ).removeClass("none");
						   $( "#signaler-ok" ).html("Vous avez déjà signalé cette chronique !");
						});
					 </script> ';
				
			}
			
		}
		
	}
	
	
	function themesAction() {

		
		$themes = new Application_Model_DbTable_Themes();
		$select = $themes->select();
		$select->setIntegrityCheck(false)
				->from( array('t'=>'themes'), array('t.libele as theme','t.id as id') )
				->joinLeft( array('c'=>'chroniques'), 'c.id_theme = t.id', array('COUNT(c.id) as chroniques') )
				->order('t.libele ASC')
				->group('t.id');
		$this->view->themes = $themes->fetchAll($select);

	}

	function themeAction() {
		
		
		$id_theme = $this->_getParam('id');
		
		//theme selectionné
		$themes = new Application_Model_DbTable_Themes();
		$this->view->theme = $themes->find($id_theme)->current();

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
		->order('c.id DESC')
		->group(array('c.id','p.id_chronique','q.id_chronique'));
		$chroniques = $chroniques->fetchAll($select);
		
		Zend_View_Helper_PaginationControl::setDefaultViewPartial('controls_page.phtml');
		$this->view->chroniques = Zend_Paginator::factory($chroniques)->setItemCountPerPage(10)->setCurrentPageNumber($this->_getParam('page'));
		
		//themes aléatoires
		$random_themes = new Application_Model_DbTable_Themes();
		$select = $random_themes->select();
		$select->setIntegrityCheck(false)
		->from('themes')
		->limit('4')
		->order('rand()');
		$random_themes = $random_themes->fetchAll($select);
		$this->view->random_themes = $random_themes;
		

		//recommendations chroniques
		$chroniques = new Application_Model_DbTable_Chroniques();
		$select = $chroniques->select();
		$select->setIntegrityCheck(false)
		->from( array('c'=>'chroniques'), array('c.id as id','c.titre as titre','c.recommendations as reco','c.nb_vue as vue','c.login_recom as login') )
		->where('c.valid = 1')
		->limit('5')
		->order('c.recommendations DESC');
		$chroniques = $chroniques->fetchAll($select);
		$this->view->reco = $chroniques;
		
	}
	
	function topAction() {
		
		
		
		
	}
	
	

	function inscriptionAction() {
	
	
		$form_lect = new Application_Form_User("",'0');
		$form_lect->setAction('inscription');
		$form_lect->setAttrib('id', 'Lecteur');
		$form_lect->submit->setLabel('Valider');
		$form_lect->submit->setAttrib('id','submitL');
		$form_lect->submit->setAttrib('disabled','true');
		$form_lect->submit->setAttrib('class','disabled btn btn-success');
		$this->view->Lecteur = $form_lect;
		
		$form_chro = new Application_Form_User("",'1');
		$form_chro->setAction('inscription');
		$form_chro->setAttrib('id', 'Chroniqueur');
		$form_chro->submit->setLabel('Valider');
		$form_chro->submit->setAttrib('id','submitC');
		$form_chro->submit->setAttrib('disabled','true');
		$form_chro->submit->setAttrib('class','disabled btn btn-success');
		$this->view->Chroniqueur = $form_chro;
		
		
		//vérif new user
		$form = new Application_Form_User();
		if ($this->_request->isPost()) {
			
			$formData = $this->_request->getPost();
			if( $form->isValid($formData)) {
				
				$users = new Application_Model_DbTable_Users();
				$select = $users->select();
				$select->setIntegrityCheck(false)
						->from('users')
						->where('email = ?', stripcslashes($form->getValue('email')));
				$users = $users->fetchRow($select);
				
				$users2 = new Application_Model_DbTable_Users();
				$select2 = $users2->select();
				$select2->setIntegrityCheck(false)
						->from('users')
						->where('pseudo = ?', stripcslashes($form->getValue('pseudo')));
				$users2 = $users2->fetchRow($select2);
				
				$f = new Zend_Filter_StripTags();
				$code_syntaxe='#^[\w.-]+@[\w.-]+\.[a-zA-Z]{2,5}$#';
				
				if( !empty($users) && $users->email ){
				
					if($form->getValue('type') == 1 ){
					
						$form_chro = new Application_Form_User("",'1');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Chroniqueur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitC');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Chroniqueur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir un nouveau mail.</span></div>";
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#mail-exist" ).dialog({
								      resizable: false,
								      width: 300,
								      height:190,
								      modal: true,
								      buttons: { Ok : function() { $(this).dialog("close"); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script> ';
						
					}elseif($form->getValue('type') == 0){
						
						$form_chro = new Application_Form_User("",'0');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Lecteur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitL');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Lecteur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir un nouveau mail.</span></div>";
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#mail-exist" ).dialog({
								      resizable: false,
								      width: 300,
								      height:190,
								      modal: true,
								      buttons: { Ok : function() { $(this).dialog("close"); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script> ';
						
					}
					
				
				}elseif( !empty($users2) && $users2->pseudo ){
				
					if($form->getValue('type') == 1 ){
					
						$form_chro = new Application_Form_User("",'1');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Chroniqueur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitC');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Chroniqueur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir un nouveau pseudo.</span></div>";
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#login-exist" ).dialog({
								      resizable: false,
								      width: 310,
								      height:170,
								      modal: true,
								      buttons: { Ok : function() { $(this).dialog("close"); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script> ';
					}elseif($form->getValue('type') == 0){
						
						$form_chro = new Application_Form_User("",'0');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Lecteur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitL');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Lecteur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir un nouveau pseudo.</span></div>";
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#login-exist" ).dialog({
								      resizable: false,
								      width: 310,
								      height:170,
								      modal: true,
								      buttons: { Ok : function() { $(this).dialog("close"); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script> ';
						
					}
					
				}elseif( stripcslashes($form->getValue('mdp')) != stripcslashes($form->getValue('mdp2')) ){
				
					if($form->getValue('type') == 1 ){
					
						$form_chro = new Application_Form_User("",'1');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Chroniqueur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitC');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Chroniqueur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir deux codes identiques.</span></div>";
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#mdp-diff" ).dialog({
								      resizable: false,
								      width: 280,
								      height:170,
								      modal: true,
								      buttons: { Ok : function() { $(this).dialog("close"); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script> ';
						
					}elseif($form->getValue('type') == 0){
						
						$form_chro = new Application_Form_User("",'0');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Lecteur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitL');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Lecteur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir deux codes identiques.</span></div>";
						echo '<script type="text/javascript">
			 					$(function() {
								   $( "#mdp-diff" ).dialog({
								      resizable: false,
								      width: 270,
								      height:160,
								      modal: true,
								      buttons: { Ok : function() { $(this).dialog("close"); } }
								   });
								   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
								});
							 </script> ';
					}
					
				}elseif( !preg_match($code_syntaxe,htmlentities( $f->filter($form->getValue('email')), ENT_NOQUOTES)) ){
					
					if($form->getValue('type') == 1 ){
						
						$form_chro = new Application_Form_User("",'1');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Chroniqueur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitC');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Chroniqueur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir une adresse mail valide(aaa777@bbb.cc).</span></div>";
						echo '<script type="text/javascript">
		 					$(function() {
				
					            $(".errors").parent().find("input").css("border","solid 1px #b94a48");
					            $(".errors").parent().find("input").css("-webkit-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
					            $(".errors").parent().find("input").css("-moz-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
					            $(".errors").parent().find("input").css("box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
						
							});
						 </script> ';
					}elseif($form->getValue('type') == 0){
						
						$form_chro = new Application_Form_User("",'0');
						$form_chro->setAction('inscription');
						$form_chro->setAttrib('id', 'Lecteur');
						$form_chro->submit->setLabel('Valider');
						$form_chro->submit->setAttrib('id','submitL');
						$form_chro->submit->setAttrib('disabled','true');
						$form_chro->submit->setAttrib('class','disabled btn btn-success');
						$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
						$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
						$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
						$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
						$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
						$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
						$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
						$form_chro->getElement('email')->setValue( $form->getValue('email'));
						$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
						$this->view->Lecteur = $form_chro;
						$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez saisir une adresse mail valide (aaa777@bbb.cc).</span></div>";
						echo '<script type="text/javascript">
		 					$(function() {
			
					            $(".errors").parent().find("input").css("border","solid 1px #b94a48");
					            $(".errors").parent().find("input").css("-webkit-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
					            $(".errors").parent().find("input").css("-moz-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
					            $(".errors").parent().find("input").css("box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				
							});
						 </script> ';
					
				}
				
				}else{
				
					$email = htmlentities( $f->filter($form->getValue('email')), ENT_NOQUOTES);
					$email = utf8_encode($email);
					
					$users = new Application_Model_DbTable_Users();
					$uniquid = uniqid();
					$row = $users->createRow();
					$row->nom = $form->getValue('nom');
					$row->prenom = $form->getValue('prenom');
					$row->jour = $form->getValue('jour');
					$row->mois = $form->getValue('mois');
					$row->annee = $form->getValue('annee');
					$row->sexe = $form->getValue('sexe');
					$row->pseudo = stripcslashes($form->getValue('pseudo'));
					$row->email = stripcslashes($email);
					$row->mdp = md5(stripcslashes($form->getValue('mdp')));
					$row->tel = stripcslashes($form->getValue('tel'));
					$row->valid = 0;
					$row->uniquid = $uniquid;
					$row->chroniqueur = $form->getValue('type');

					if($row->save()){

						$conteneur='<html>
									<body>
										Cher/Ch&egrave;re membre,<br/><br/>
		
										Chronique-Time vous remercie pour votre inscription, et vous souhaite la bienvenue dans la communaut&eacute; ChroniqueTime <br/>
										Votre compte utilisateur a bien &eacute;t&eacute; cr&eacute;&eacute; !<br/>
										Gr&acirc;ce &agrave; cette inscription il vous est maintenant plus facile de consulter vos chroniques, les sauvegarder, les noter, etc...<br/>
										Il vous suffit maintenant de <a href="http://chroniquetime.fr/public/index/valide/cle/'.$uniquid.'/mail/'.stripcslashes($form->getValue('email')).'">cliquer sur ce lien</a> afin de valider votre compte ChroniqueTime. <br /><br />
										<br/> 
										A bient&ocirc;t, et bonne lecture sur <a href="http://chroniquetime.fr">CHRONIQUE-TIME</a><br/>
										Cordialement,<br/><br/>
										L\' &eacute;quipe Baildy<br/>
									</body>
									</html>';

						$mail = new Zend_Mail();
						$mail->setBodyHtml($conteneur);
						$mail->setFrom('contact@chroniquetime.fr', 'Chronique T.');
						$mail->addTo('"'.$f->filter($form->getValue('email')).'"', 'Vous');
						$mail->setSubject('Inscription ChroniqueTime');
						if( $mail->send() ){
							echo $this->_redirect('index/index/register/1');
						}
						
					}
					
				}
				
			}else{
				
				echo '<script type="text/javascript">
	 					$(function() {
					
				            $(".errors").parent().find("input").css("border","solid 1px #b94a48");
				            $(".errors").parent().find("input").css("-webkit-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				            $(".errors").parent().find("input").css("-moz-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				            $(".errors").parent().find("input").css("box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
						
						});
					 </script> ';
				
				if($form->getValue('type') == 1 ){
					
					$form_chro = new Application_Form_User("",'1');
					$form_chro->setAction('inscription');
					$form_chro->setAttrib('id', 'Chroniqueur');
					$form_chro->submit->setLabel('Valider');
					$form_chro->submit->setAttrib('id','submitC');
					$form_chro->submit->setAttrib('disabled','true');
					$form_chro->submit->setAttrib('class','disabled btn btn-success');
					$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
					$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
					$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
					$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
					$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
					$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
					$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
					$form_chro->getElement('email')->setValue( $form->getValue('email'));
					$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
					$this->view->Chroniqueur = $form_chro;
					$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez compléter tout les champs correctement.</span></div>";
					echo '<script type="text/javascript">
	 					$(function() {
			
				            $(".errors").parent().find("input").css("border","solid 1px #b94a48");
				            $(".errors").parent().find("input").css("-webkit-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				            $(".errors").parent().find("input").css("-moz-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				            $(".errors").parent().find("input").css("box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
					
						});
					 </script> ';
				}elseif($form->getValue('type') == 0){
					
					$form_chro = new Application_Form_User("",'0');
					$form_chro->setAction('inscription');
					$form_chro->setAttrib('id', 'Lecteur');
					$form_chro->submit->setLabel('Valider');
					$form_chro->submit->setAttrib('id','submitL');
					$form_chro->submit->setAttrib('disabled','true');
					$form_chro->submit->setAttrib('class','disabled btn btn-success');
					$form_chro->getElement('nom')->setValue( $form->getValue('nom'));
					$form_chro->getElement('prenom')->setValue( $form->getValue('prenom'));
					$form_chro->getElement('jour')->setValue( $form->getValue('jour'));
					$form_chro->getElement('mois')->setValue( $form->getValue('mois'));
					$form_chro->getElement('annee')->setValue( $form->getValue('annee'));
					$form_chro->getElement('pseudo')->setValue( $form->getValue('pseudo'));
					$form_chro->getElement('sexe')->setValue( $form->getValue('sexe'));
					$form_chro->getElement('email')->setValue( $form->getValue('email'));
					$form_chro->getElement('tel')->setValue( $form->getValue('tel'));
					$this->view->Lecteur = $form_chro;
					$this->view->msg = "<div class='alert alert-error' style='width:900px;margin: -20px auto 30px;'>Échec de l'inscription ! <span style='font-size:12px;'>Veuillez compléter tout les champs correctement.</span></div>";
					echo '<script type="text/javascript">
	 					$(function() {
		
				            $(".errors").parent().find("input").css("border","solid 1px #b94a48");
				            $(".errors").parent().find("input").css("-webkit-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				            $(".errors").parent().find("input").css("-moz-box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
				            $(".errors").parent().find("input").css("box-shadow","inset 0 1px 1px rgba(0, 0, 0, 0.075), 0 0 6px #d59392");
			
						});
					 </script> ';
					
				}
				
			}
			
		}// end if verif
		
		//msg err
		$err = $this->_getParam('err');
		if( !empty($err) && $err == "1" ) {
				
			echo '<script type="text/javascript">
 					$(function() {
					   $( "#mail-exist" ).dialog({
					      resizable: false,
					      width: 300,
					      height:190,
					      modal: true,
					      buttons: { Ok : function() { $(this).dialog("close"); } }
					   });
					   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
					});
				 </script>
				';
		}elseif( !empty($err) && $err == "2" ) {
				
			echo '<script type="text/javascript">
 					$(function() {
					   $( "#login-exist" ).dialog({
					      resizable: false,
					      width: 270,
					      height:160,
					      modal: true,
					      buttons: { Ok : function() { $(this).dialog("close"); } }
					   });
					   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
					});
				 </script>
				';
		}elseif( !empty($err) && $err == "3" ) {
				
			echo '<script type="text/javascript">
 					$(function() {
					   $( "#mdp-diff" ).dialog({
					      resizable: false,
					      width: 270,
					      height:160,
					      modal: true,
					      buttons: { Ok : function() { $(this).dialog("close"); } }
					   });
					   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
					});
				 </script>
				';
		}elseif( !empty($err) && $err == "9" ) {
				
			echo '<script type="text/javascript">
 					$(function() {
					   $( "#register-ok" ).dialog({
					      resizable: false,
					      width: 270,
					      height:190,
					      modal: true,
					      buttons: { Ok : function() { $(this).dialog("close"); } }
					   });
					   $(".ui-dialog-buttons .ui-dialog-buttonpane button").attr("class","btn");
					});
				 </script>
				';
		}

	}

	
	function valideAction(){
		
		$cle = $this->_getParam('cle');
		$mail = $this->_getParam('mail');
		$users = new Application_Model_DbTable_Users();
		$select = $users->select();
		$select->setIntegrityCheck(false)
				->from('users')
				->where('email = ?', $mail)
				->where('uniquid = ?', $cle);
		
		if($users->fetchRow($select)) {
			
			$data = array();
			$data['valid'] = 1;
			$where = "uniquid = '$cle'";
			$users = new Application_Model_DbTable_Users();
			if($users->update($data, $where)) {

				echo $this->_redirect('index/index/register/2');
				
			}
		}
		
		
				
		if($users->validerIdscription($cle, $mail))
			$this->view->message=" Merci, votre compte client BoxyMed est bien activ&eacute; !";
		
		
	}
	
//end Controller -----
}

