<?php

class IndexController extends Swift_Controller_Action
{

    public function init(){
      
    }
    

	/**
	* Page to logout
	*/
	public function logoutAction()
    {
    	$storage = new Zend_Auth_Storage_Session();
        $storage->clear();
    	//Disable login cookie
        setcookie('autl','',time()-31536000,'','.'.$_SERVER['HTTP_HOST']);
        
        
        $this->_redirect('/');
    }
    
    /**
	* Page to login
	*/
    public function indexAction(){
    	$this->view->errors = array();
    	$goto = $this->getRequest()->getParam('goto');
	    if(@$this->_me){
	    	if($goto){
	    		$this->_redirect("/$goto");
	    	} else {
	    		$this->_redirect("/magazine");
	    	}
	    }
	    $this->view->goto = $goto;
    	if($this->getRequest()->isPost()){
	    	//Get form data from post array
	        $data = $this->_request->getPost();
	        if($data['email'] == '' || $data['password'] == ''){
	        	 $this->view->errors[] = "Please provide your email address and password.";
	        	 return false;
	        }
	    	//Log user in to session 
	        $users = new Users;
	        $auth = Zend_Auth::getInstance();
	        $authAdapter = new Zend_Auth_Adapter_DbTable($users->getAdapter(),'users');
	        $authAdapter->setIdentityColumn('email')
	                    ->setCredentialColumn('password');
	        $authAdapter->setIdentity($data['email'])
	                    ->setCredential(sha1($data['password']));
	        $result = $auth->authenticate($authAdapter);
	        if($result->isValid()){
	        	Zend_Session::rememberMe(31536000);
	        	
	        	$credentials = base64_encode(serialize(array('email'=>$data['email'],'password'=>sha1($data['password'])))); 
	        	         
            	//Set login cookie
            	setcookie('autl',$credentials,time()+31536000,'',$_SERVER['HTTP_HOST']);
            	
            	
            
	            $storage = new Zend_Auth_Storage_Session();
	            $storage->write($authAdapter->getResultRowObject());
	            $this->_redirect($data['goto']);
	        } else {
	             $this->view->errors[] = "Invalid email or password. Please try again.";
	            $user = $users->getUserByEmail($data['email']);
 
	        } 
        
        }
    }
    
    
    public function addedituserAction(){
    	if(@$this->_me->type < 9){
    		$this->_redirect("/");
    	}
    	$id = $this->getRequest()->getParam('id',false);
    	$usersModel = new Users;
    	
    	
    	
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		
    		$method = $data['method'];
    		unset($data['method']);
    		
    		if($data['password'] == ''){
    			unset($data['password']);
    		} else {
    			$data['password'] = sha1($data['password']);
    		}
    		
    		if($method=='create'){
    			if($data['email'] <> '' && $data['password'] <> '' && $data['name'] <> ''){
	    			if($usersModel->insert($data)){
	    				$this->view->success = "User successfully created.";
	    			} 
    			} else {
    				$this->view->errors = array('Please provide email, password and name');
    			}
    			
    		}
    		
    		if($method=='update'){
    			if($usersModel->updateUser($id,$data)){
    				$this->view->success = "User successfully updated.";
    			}
    		}
    		
    		if($method == 'delete'){
    			$where = 'id='.$id;
    			if($usersModel->delete($where)){
    				//$this->getRequest()->clearParams();
    				$this->view->success = "User successfully deleted";
    				$this->_forward('addedituser');
    			}
    		}
    		
    		if(!$this->view->success && !$this->view->errors){
    			$this->view->errors = array('An Error Occurred. Please Try Again.');
    			$this->view->data = $data;
    		}
    		
    		
    		
    	}
    	if($id){
    			$this->view->editUser = $usersModel->getUser($id);
    			$this->view->data = $this->view->editUser;
    		}
    		$this->view->allUsers = $usersModel->getAll();
    	
    }

	 public function addeditclientAction(){
    	if(!$this->_me){
    		$this->_redirect("/");
    	}
    	$id = $this->getRequest()->getParam('id',false);
    	$clientsModel = new Clients;
    	
    	
    	
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		
    		$method = $data['method'];
    		unset($data['method']);
    		
    	
    		
    		if($method=='create'){
    			if($data['company'] <> ''){
    				$data['user'] = $this->_me->id;
	    			if($clientsModel->insert($data)){
	    				$this->view->success = "Client successfully created.";
	    			} 
    			} else {
    				$this->view->errors = array('Please provide email, and company.');
    			}
    			
    		}
    		
    		if($method=='update'){
    			if($clientsModel->updateRecord($id,$data)){
    				$this->view->success = "Client successfully updated.";
    			}
    		}
    		
    		if($method == 'delete'){
    			$where = 'id='.$id;
    			if($clientsModel->delete($where)){
    				//$this->getRequest()->clearParams();
    				$this->view->success = "Client successfully deleted";
    				$this->_forward('addeditclient');
    			}
    		}
    		
    		
    		if(!$this->view->success && !$this->view->errors){
    			$this->view->errors = array('An Error Occurred. Please Try Again.');
    			$this->view->data = $data;
    		}
    		
    		
    		
    	}
    	if($id){
    			$this->view->editClient = $clientsModel->getById($id);
    			$this->view->data = $this->view->editClient;
    		}
    	if(@$this->_me->type == 9){
    		$this->view->allClients = $clientsModel->getAll();
    	} else {
    		$this->view->allClients = $clientsModel->getAllByUser($this->_me->id);
    	}
    	$usersModel= new Users;
    	$this->view->users = $usersModel->getAll();
    }
    
    
    
    
    public function addeditpublisherAction(){
    	if(!$this->_me){
    		$this->_redirect("/");
    	}
    	$id = $this->getRequest()->getParam('id',false);
    	$publisherModel = new Publishers;
    	
    	
    	
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		
    		$method = $data['method'];
    		unset($data['method']);
    		
    	
    		
    		if($method=='create'){
    			if($data['company'] <> ''){
	    			if($publisherModel->insert($data)){
	    				$this->view->success = "Publisher successfully created.";
	    			} 
    			} else {
    				$this->view->errors = array('Please provide email, and company.');
    			}
    			
    		}
    		
    		if($method=='update'){
    			if($publisherModel->updateRecord($id,$data)){
    				$this->view->success = "Publisher successfully updated.";
    			}
    		}
    		
    		if($method == 'delete'){
    			$where = 'id='.$id;
    			if($publisherModel->delete($where)){
    				//$this->getRequest()->clearParams();
    				$this->view->success = "Publisher successfully deleted";
    				$this->_forward('addeditpublisher');
    			}
    		}
    		
    		if(!$this->view->success && !$this->view->errors){
    			$this->view->errors = array('An Error Occurred. Please Try Again.');
    			$this->view->data = $data;
    		}
    		
    		
    		
    	}
    	if($id){
    			$this->view->editPublisher = $publisherModel->getById($id);
    			$this->view->data = $this->view->editPublisher;
    		}
    	$this->view->allPublishers = $publisherModel->getAll();
    }
    
    
    public function myaccountAction(){
    	if(!$this->_me){
    		$this->_redirect('/');
    	}
    	$usersModel = new Users;
    	
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		if($data['password'] == ''){
    			unset($data['password']);
    		} else {
    			$data['password'] = sha1($data['password']);
    		}
    		
    		if($usersModel->updateUser($this->_me->id,$data)){
    			$this->view->success = "Settings Saved.";
    		}
    		
    	}
    	
    	$this->view->userData = $usersModel->getUser($this->_me->id);
    }

    
}

