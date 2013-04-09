<?php
class Zend_View_Helper_ProfileLink extends Zend_View_Helper_Abstract {
  
public function profileLink() {
        
$helperUrl = new Zend_View_Helper_Url ( );
       $auth = Zend_Auth::getInstance ();
       if ($auth->hasIdentity () AND $auth->getIdentity()->statut == 2) {
           $username = $auth->getIdentity()->username;
           $logoutLink = $helperUrl->url ( array ('controller' => 'auth', 'action' => 'logout') );
          return '<h2><i>Bonjour : ' . $username . ' </i></h2>(<a align="center" href="' . $logoutLink . '" style="color:red ; size:3px;">D&eacute;connexion</a>)<br><br>';
      }elseif ($auth->hasIdentity () AND $auth->getIdentity()->statut == 1){
      	$username = $auth->getIdentity()->username;
           $logoutLink = $helperUrl->url ( array ('controller' => 'auth', 'action' => 'logout') );
          return '<p class="p7" style="vertical-align:middle;"><i> Bonjour </i> <b style="color:red;">'. $username .'</b> | <i><a href="'. $logoutLink .'" style="size:3px;"> D&eacute;connexion <img src="../../public/images/icon_deco.png"></a></i> </p>';
      	
      }
   }
}

//
//
//elseif ($auth->hasIdentity () AND $auth->getIdentity()->statut == 1){
//      	$username = $auth->getIdentity()->username;
//           $logoutLink = $helperUrl->url ( array ('controller' => 'auth', 'action' => 'logout') );
//           $this->baseUrl = $this->_request->getBaseUrl();
//           $img = $this->baseUrl("/images/icon_deco.png");
//          return '<p style="vertical-align:middle;"><i> Bonjour </i> <b style="color:red;">'. $username .'</b> | <i><a href="'. $logoutLink .'" style="size:3px;"> Déconnexion <img src="'.$img.'"></a></i> </p>';
//      	
//      }


//elseif ($auth->hasIdentity () AND $auth->getIdentity()->statut == 1){
//      	$username = $auth->getIdentity()->username;
//           $logoutLink = $helperUrl->url ( array ('controller' => 'auth', 'action' => 'logout') );
//          return '<p style="vertical-align:middle;"><i> Bonjour </i> <b style="color:red;">'. $username .'</b> | <i><a href="'. $logoutLink .'" style="size:3px;"> Déconnexion <img src="../../public/images/icon_deco.png"></a></i> </p>';
//      	
//      }