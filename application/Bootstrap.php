<?php

class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{
    protected function _initDoctype()
    {
        date_default_timezone_set('America/New_York');
        $this->bootstrap('view');
        $view = $this->getResource('view');
        $view->doctype('XHTML1_STRICT');
        ini_set("session.cookie_lifetime",0);
        
    }
    
    protected function _initRouter() {
      
    		$routeConfig = new Zend_Config_Ini ( APPLICATION_PATH . '/configs/routes.ini' );
		    $router = new Zend_Controller_Router_Rewrite ();
		    $router->addConfig ( $routeConfig, 'routes' );
//     		var_dump($router);
		    Zend_Registry::set ( 'routeConfig', $routeConfig );
		    Zend_Controller_Front::getInstance ()->setRouter ( $router );
    	
        
    }
    

    

}

