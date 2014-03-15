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
    	//set log
    	$this->logLoginStatus('loggedOut');
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
            	//set log
            	$this->logLoginStatus('loggedIn');
	            $storage = new Zend_Auth_Storage_Session();
	            $storage->write($authAdapter->getResultRowObject());
	            $this->_redirect($data['goto']);
	        } else {
	             $this->view->errors[] = "Invalid email or password. Please try again.";
	             //set log
                 $this->logLoginStatus("invalidCredential, username:".$data['email']."");
	            $user = $users->getUserByEmail($data['email']);
 
	        } 
        
        }
    }


    public function logLoginStatus($action)
    {
    	$date = new Zend_Date();
    	$loguser = new Loguser;
		$systemInfo=array("HTTP_USER_AGENT"=>$_SERVER["HTTP_USER_AGENT"],"SERVER_NAME"=>$_SERVER["SERVER_NAME"],"SERVER_ADMIN"=>$_SERVER["SERVER_ADMIN"],"REMOTE_ADDR"=>$_SERVER["REMOTE_ADDR"],"SERVER_ADDR"=>$_SERVER["SERVER_ADDR"],"HTTP_HOST"=>$_SERVER["HTTP_HOST"],"HTTP_REFERER"=>$_SERVER["HTTP_REFERER"]);
    	if(isset($this->_me->id))
    		$userId = $this->_me->id;
    	else 
    		$userId =1;
    		
    	$data = array("userid"=>$userId,"status"=>$action,"systemInfo"=>stripslashes(json_encode($systemInfo)),"created"=>$date->get('YYYY-MM-dd HH:mm:ss'),"createdById"=>$userId,"updatedById"=>$userId);
    	$loguser->addLogUser($data);
    	 
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
		
		
		
		$clientData = array();
		$clientContacts = array();
		
    	$clientsModel = new Clients;
		$clientsContactModel = new ClientContacts;
		$clientGroupModel = new ClientGroup;
		
		$clientsCols = $clientsModel->info(Zend_Db_Table_Abstract::COLS);
		$clientsContactCols = $clientsContactModel->info(Zend_Db_Table_Abstract::COLS);
    	
		$clientGroups = $clientGroupModel->getAll();
		$this->view->clientGroups = $clientGroups;
		
    	try{
			//$writer = new Zend_Log_Writer_Stream($_SERVER["DOCUMENT_ROOT"].'/../logs/log.txt');
			//$writer = new Zend_Log_Writer_Stream('/logs/log.txt');
			//$logger = new Zend_Log($writer);
		}
		catch(Exception $e){
			$this->view->errors = array($e);
		}
		
    	if($this->getRequest()->isPost()){
		
    		$data = $this->getRequest()->getPost();
    				
    		$method = $data['method'];
    		unset($data['method']);
    		
			/*if (is_array($data)) {
				foreach ($data as $key => $value) {
				$logger->info($key . '-' .$value);
			}
			}*/

			/*if($data['name'] == ''){
				$data['name'] = 'some name';
			}*/				
			
			// EXTRACTING CLIENT DATA
			foreach($clientsCols AS $col){	
					if(isset($data[$col]))
						$clientData[$col] = $data[$col];
			}
			
			//$clientData['name'] = 'some name';
			
			$i = 1;
			// EXTRACTING CLIENT CONTACT DATA
			while($i < 11){
   					$clientContact = array();
					
	   				foreach($clientsContactCols AS $col){
	   					if(isset($data[$col."_".$i]) && $data[$col."_".$i] <> ''){
							$clientContact[$col] = $data[$col."_".$i];
	   					}
						//unset($data[$col."_".$i]);
	   				}
	   				if(!$this->view->errors && isset($data['name_'.$i]) && $data['name_'.$i] <> ''){						
						$clientContacts[] = $clientContact;
	   				}
	   				$i++;
   			}
		
			// CREATE
			if($method=='create'){
				if($clientData['company'] <> ''){
					//validate
					$this->validateCountryState($clientData);
					$clientData['user'] = $this->_me->id;
					try{
						if($clientData['country'] == "US" && empty($clientData['state']))
						{
								$this->view->errors = array('Please provide State.');
						}
						else {
							$clientId = $clientsModel->insert($clientData);
							if($clientId){
								foreach($clientContacts AS $clientContact){
									$clientContact['client_id'] = $clientId;
									$clientsContactModel->insert($clientContact);
								}
								$this->view->success = "Client successfully created.";
							}
						}
					}
					catch(Exception $e){
						$this->view->errors = array($e);
					}					
				} else {
					$this->view->errors = array('Please provide email, and company.');
				}
			}
			
			// UPDATE
			if($method=='update'){
				try{
					$clientData['id'] = $id;
					if($clientsModel->updateRecord($id,$clientData)){
						foreach($clientContacts AS $clientContact){
							//$clientContact['client_id'] = $id;
							if(isset($clientContact['id'])){
								$clientsContactModel->updateRecord($clientContact['id'], $clientContact);
							}
							else{
								//$clientContact['id']
								$clientContact['client_id'] = $id;
								$clientsContactModel->insert($clientContact);
							}
						}
						$this->view->success = "Client successfully updated.";
					}
				}
				catch(Exception $e){
					$this->view->errors = array($e);
				}
			}
			
			// DELETE
			if($method == 'delete'){
				
				try{
					$where = 'id='.$id;
					$clientsModel->delete($where);
					//if($clientsModel->delete($where)){
						$where = 'client_id='.$id;
						$clientsContactModel->delete($where);
						//if($clientsContactModel->delete($where))
						//{
							$this->view->success = "Client successfully deleted";
						//}
					$this->getRequest()->clearParams();
					$this->view->success = "Client successfully deleted";
					//$this->_forward('addeditclient');
					//}
				}
				catch(Exception $e){
					$this->view->errors = array($e);
				}
				
			}
						
			if(!$this->view->success && !$this->view->errors){
			
				$this->view->errors = array('An Error Occurred. Please Try Again.');
				$this->view->data = $data;
			}
    		
    	}

		// RETURNING BACK TO THE SCREEN
    	if($id){
    			$this->view->editClient = $clientsModel->getById($id);
    			$this->view->data = $this->view->editClient;
				
				$clientContacts = $clientsContactModel->getByClientId($id);
				
				$data = array();
				
				foreach($clientsCols AS $col){
						$data[$col] = $this->view->editClient[$col];
				}
				
				$i=1;
				while($i < 11){
					foreach($clientsContactCols AS $col){
								$data[$col."_".$i] = '';
						}
						$i++;
				}
				
				if(isset($clientContacts))
				{
					$i=1;
					foreach($clientContacts AS $contact){	
						foreach($clientsContactCols AS $col){
							
							if(isset($contact[$col]) && $contact[$col] <> ''){
								$data[$col."_".$i] = $contact[$col];
							}
							else
								$data[$col."_".$i] = '';
						}
						$i++;
					}
				}
				
				$this->view->data = $data;
				
    	}
			
    	if(@$this->_me->type == 9){
    		$this->view->allClients = $clientsModel->getAll();
    	} else {
    		$this->view->allClients = $clientsModel->getAllByUser($this->_me->id);
    	}
    	$usersModel= new Users;
    	$this->view->users = $usersModel->getAll();
		
    }
 
    public function validateCountryState($clientData)
    {
    	if($clientData['country'] == "US")
    	{
    		if(empty($clientData['state']))
    		{
    			$this->view->errors = array('Please provide State.');
    			return false;
    		}
    	}
    	
    }
    
    public function addeditpublisherAction()
	 {
    	if(!$this->_me){
    		$this->_redirect("/");
    	}
    	$id = $this->getRequest()->getParam('id',false);
	
		try{
			//$writer = new Zend_Log_Writer_Stream($_SERVER["DOCUMENT_ROOT"].'/../logs/log.txt');
			//$writer = new Zend_Log_Writer_Stream('/logs/log.txt');
			//$logger = new Zend_Log($writer);
		}
		catch(Exception $e){
			$this->view->errors = array($e);
		}	
		
		
		$publisherData = array();
		$publisherContacts = array();
		
    		$publishersModel = new Publishers;
		$publishersContactModel = new PublisherContacts;
		$publisherGroupModel = new PublisherGroup;
		
		$publishersCols = $publishersModel->info(Zend_Db_Table_Abstract::COLS);
		$publishersContactCols = $publishersContactModel->info(Zend_Db_Table_Abstract::COLS);
		$publisherGroups = $publisherGroupModel->getAll();
		
		//$this->view->publisherGroups = array();
		$this->view->publisherGroups = $publisherGroups;
    	
		
    	if($this->getRequest()->isPost()){
		
    		$data = $this->getRequest()->getPost();
    				
    		$method = $data['method'];
    		unset($data['method']);
    		
			/*if (is_array($data)) {
				foreach ($data as $key => $value) {
				$logger->info($key . '-' .$value);
			}
			}*/

			//if($data['name'] == ''){
				//$data['name'] = 'some name';
			//}				
			
			// EXTRACTING PUBLISHER DATA
			foreach($publishersCols AS $col){
				if(isset($data[$col])){
					$publisherData[$col] = $data[$col];
					
					if($col == 'company')
						$publisherData['name'] = $data['company'];
				}
					
			}
			
			// EXTRACTING PUBLISHER CONTACT DATA
			$i = 1;	
			while($i < 11){
   					$publisherContact = array();
					
	   				foreach($publishersContactCols AS $col){
	   					if(isset($data[$col."_".$i]) && $data[$col."_".$i] <> ''){
							$publisherContact[$col] = $data[$col."_".$i];
	   					}
						//unset($data[$col."_".$i]);
	   				}
	   				if(!$this->view->errors && isset($data['name_'.$i]) && $data['name_'.$i] <> ''){
						$publisherContacts[] = $publisherContact;
	   				}
	   				$i++;
   			}
		
			// CREATE
			if($method=='create'){
				
				if($publisherData['company'] <> '' && trim($publisherData['circulation_rate_base']) <> ''){
					try{
						//validate
						if($publisherData['country'] == "US" && empty($publisherData['state']))
						{
							$this->view->errors = array('Please provide State.');
						}
						else
						{
							$publisherId = $publishersModel->insert($publisherData);
							if($publisherId){
								foreach($publisherContacts AS $publisherContact){
									$publisherContact['publisher_id'] = $publisherId;
									$publishersContactModel->insert($publisherContact);
								}
								$this->view->success = "Publisher successfully created.";
							}
						}
					}
					catch(Exception $e){
						$this->view->errors = array($e);
					}					
				} else {
					$this->view->errors = array('Please provide email, and company, and rate.');
				}
			}
			// UPDATE
			if($method=='update'){
				if($publisherData['company'] <> '' && strlen($publisherData['circulation_rate_base']) > 0){
					try{
						$publisherData['id'] = $id;
						if($publishersModel->updateRecord($id,$publisherData)){
							foreach($publisherContacts AS $publisherContact){
								//$publisherContact['client_id'] = $id;
								if(isset($publisherContact['id'])){
									$publishersContactModel->updateRecord($publisherContact['id'], $publisherContact);
								}
								else{
									//$publisherContact['id']
									$publisherContact['publisher_id'] = $id;
									$publishersContactModel->insert($publisherContact);
								}
							}
							$this->view->success = "Publisher successfully updated.";
						}
					}
					catch(Exception $e){
						$this->view->errors = array($e);
					}
				} else {
					$this->view->errors = array('Please provide email, and company, and rate.');
				}
			}
			
			// DELETE
			if($method == 'delete'){
				
				try{
					$where = 'id='.$id;
					$publishersModel->delete($where);
					//if($clientsModel->delete($where)){
						$where = 'publisher_id='.$id;
						$publishersContactModel->delete($where);
						//if($clientsContactModel->delete($where))
						//{
							$this->view->success = "Publisher successfully deleted";
						//}
					$this->getRequest()->clearParams();
					$this->view->success = "Publisher successfully deleted";
					//$this->_forward('addeditpublisher');
					//}
				}
				catch(Exception $e){
					$this->view->errors = array($e);
				}
				
			}
						
			if(!$this->view->success ){
				if(!$this->view->errors)
					$this->view->errors = array('An Error Occurred. Please Try Again.');
					
				$this->view->data = $data;
			}
    		
    	}

	// RETURNING BACK TO THE SCREEN
    	if($id){
    			$this->view->editPublisher = $publishersModel->getById($id);
    			$this->view->data = $this->view->editPublisher;
				
				$publisherContacts = $publishersContactModel->getByPublisherId($id);
				
				$data = array();
				
				foreach($publishersCols AS $col){
						$data[$col] = $this->view->editPublisher[$col];
				}
				
				$i=1;
				while($i < 11){
					foreach($publishersContactCols AS $col){
								$data[$col."_".$i] = '';
						}
						$i++;
				}
				
				if(isset($publisherContacts))
				{
					$i=1;
					foreach($publisherContacts AS $contact){	
						foreach($publishersContactCols AS $col){
							
							if(isset($contact[$col]) && $contact[$col] <> ''){
								$data[$col."_".$i] = $contact[$col];
							}
							else
								$data[$col."_".$i] = '';
						}
						$i++;
					}
				}
				
				$this->view->data = $data;
				
    	}
			
    	
    	$this->view->allPublishers = $publishersModel->getAll();
    	
		
    }
    
      
    public function addeditclientgroupAction()
    {
    	if(!$this->_me){
    		$this->_redirect("/");
    	}
    	$id = $this->getRequest()->getParam('id',false);
    	$clientgroupModel = new ClientGroup;
    	
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		
    		$method = $data['method'];
    		unset($data['method']);
    				
    		if($method=='create')
				{
    			
    			if($data['clientgroupname'] <> '')
    			{
    				$data['user'] = $this->_me->id;
    				if($data['country'] == "US" && empty($data['state']))
    				{
    					$this->view->errors = array('Please provide State.');
    				}
    				else
    				{
		    			if($clientgroupModel->insert($data)){
		    				$this->view->success = "Client Group Name successfully created.";
		    			} 
    					
    				}
    			} else {
    				$this->view->errors = array('Please provide Client Group Name.');
    			}
    		 		}
    	    		
    		if($method=='update'){
    			if($clientgroupModel->updateRecord($id,$data)){
    				$this->view->success = "Client Group Name successfully updated.";
    			}
    		}
    		
    		if($method == 'delete'){
    			$where = 'id='.$id;
    			if($clientgroupModel->delete($where)){
    				//$this->getRequest()->clearParams();
    				$this->view->success = "Client Group Name successfully deleted";
    				$this->_forward('clientgroup');
    			}
    		}
    	
		if(!$this->view->success && !$this->view->errors){
    			$this->view->errors = array('An Error Occurred. Please Try Again.');
    			$this->view->data = $data;
    		}
    	    		   		
    	}
    	if($id){
    			$this->view->editClientGroup = $clientgroupModel->getById($id);
    			$this->view->data = $this->view->editClientGroup;
				
    		}
    	if(@$this->_me->type == 9){
    		$this->view->allClients = $clientgroupModel->getAll();
    	} else {
    		$this->view->allClients = $clientgroupModel->getAllByUser($this->_me->id);
    	}
    	$usersModel= new Users;
    	$this->view->users = $usersModel->getAll();
    }		

       
    public function addeditpublishergroupAction()
    {
    	if(!$this->_me){
    		$this->_redirect("/");
    	}
    	$id = $this->getRequest()->getParam('id',false);
    	$publishergroupModel = new PublisherGroup;
    	
    	if($this->getRequest()->isPost()){
    		$data = $this->getRequest()->getPost();
    		
    		$method = $data['method'];
    		unset($data['method']);
    				
    		if($method=='create')
				{
						
    			if(($data['publishergroupname'] <> '') && ($data['email'] <> ''))
    			{
    				$data['user'] = $this->_me->id;
    				
					if($data['country'] == "US" && empty($data['state']))
					{
							$this->view->errors = array('Please provide State.');
					}
					else 
					{
		    			if($publishergroupModel->insert($data)){
		    				$this->view->success = "Publisher Group Name successfully created.";
		    			} 
					}
    			} else {
    				$this->view->errors = array('Please provide Publisher Group Name and Email.');
    			}
    		 		}
    	    		
    		if($method=='update'){
    			if($publishergroupModel->updateRecord($id,$data)){
    				$this->view->success = "Publisher Group Name successfully updated.";
    			}
    		}
    		
    		if($method == 'delete'){
    			$where = 'id='.$id;
    			if($publishergroupModel->delete($where)){
    				//$this->getRequest()->clearParams();
    				$this->view->success = "Publisher Group Name successfully deleted";
    				$this->_forward('publishergroup');
    			}
    		}
    	
		if(!$this->view->success && !$this->view->errors){
    			$this->view->errors = array('An Error Occurred. Please Try Again.');
    			$this->view->data = $data;
    		}
    	    		   		
    	}
    	if($id){
    			$this->view->editPublisherGroup = $publishergroupModel->getById($id);
    			$this->view->data = $this->view->editPublisherGroup;
				
    		}
    	if(@$this->_me->type == 9){
    		$this->view->allClients = $publishergroupModel->getAll();
    	} else {
    		$this->view->allClients = $publishergroupModel->getAllByUser($this->_me->id);
    	}
    	$usersModel= new Users;
    	$this->view->users = $usersModel->getAll();
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

