<?php
class LecteurController extends Zend_Controller_Action {
	
	
	function preDispatch() {
		
		$auth = Zend_Auth::getInstance();
		$ok = $auth->hasIdentity();
		if(!$ok OR $auth->getIdentity()->chroniqueur != 0) $this->_redirect('index');
		 
	}
	
    public function init() {
    	$this->initView();
    	$this->view->baseUrl = $this->_request->getBaseUrl();
    	
    	
    }
    
    
    function compteAction(){
    
    
    }
    
}