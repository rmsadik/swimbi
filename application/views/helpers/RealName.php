<?php

class Zend_View_Helper_RealName {

    public $view;

    public function setView(Zend_View_Interface $view) {
        $this->view = $view;
    }

    public function realName($id) {
    	$usersModel = new Users;
    	$user = $usersModel->getUser($id);
    	
    	return $user->name;
    }
}
?>
