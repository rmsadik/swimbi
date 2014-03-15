<?php
class AdminController extends Swift_Controller_Action
{

    public function init(){
     	 if(!$this->_me->type == 9){
   				$this->redirect("/");
   			}
    }

	/**
	* Page
	*/
    public function levelsAction(){
   			$settingsModel = new Settings;
   			
   			if($this->getRequest()->isPost()){
   				$data = $this->getRequest()->getPost();
   				$where = 'id > 0';
   				
   				$settings = array();
   				foreach($data AS $key => $value){
   					$settings[$key] = $value;
   				}
   				
   				$settings = json_encode($settings);
   				$newData['level_commissions'] = $settings;
   				$settingsModel->update($newData,$where);
				$this->view->success = "Created Successfully.";
   			}
   			
   			$this->view->data = (array) $settingsModel->getLevelCommissions();

    }
    

    
}
