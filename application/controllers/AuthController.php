<?php

class AuthController extends Zend_Controller_Action {
	
	
	public function init(){
		
		$this->initView();
		$this->view->baseUrl = $this->_request->getBaseUrl();
		$this->_helper->layout->setLayout("layout_logout");
		
	}
	
	public function preDispatch(){

		$action = $this->_request->getActionName();
		if($action != "logout"){
			
			$auth = Zend_Auth::getInstance();
			if(isset($auth->getIdentity()->login)){
				
				if($auth->getIdentity()->statut == 1){
					$this->_redirect('/admin');
				}
			}
			
		}

	}
	
	
	function indexAction() {
		
		$this->_redirect('/auth/login');
		
	}
	
	
	function loginAction()	{
		
		
		$connexion = new Application_Form_Connexion();
		$this->view->connexion = $connexion;
		
		
		if ($this->_request->isPost()) {
			
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
				$authAdapter->setTableName('administrateurs'); 
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
					$statut = $auth->getIdentity()->email;
					
					if($statut) {
						
						//inserer date dern connexion
						$users = new Application_Model_DbTable_Administrateurs();
						
						$data = array();
						$data['date_connexion'] = date('Y-m-d H:i:s');
						$where  = " id = ".$auth->getIdentity()->id;
							
						if( $users->update($data, $where) ){
							
							$this->_redirect('/admin');
							
						}
					
					
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
	
		$this->render();
		
	}
	
	function logoutAction()
	{
		//je dit au singleton Zend_Auth de supprimer ses données.
		Zend_Auth::getInstance()->clearIdentity();
		$this->_redirect('/index');
	}

	
	
	
	
	
}