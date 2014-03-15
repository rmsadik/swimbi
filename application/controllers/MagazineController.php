<?php
class MagazineController extends Swift_Controller_Action
{

    public function init(){
    }

	/**
	* Page
	*/
    public function formAction(){
   			if(!$this->_me){
   				$this->redirect("/");
   			}
   			
			//$writer = new Zend_Log_Writer_Stream($_SERVER["DOCUMENT_ROOT"].'/../logs/log.txt');
			//$writer = new Zend_Log_Writer_Stream('/../logs/log.txt');
			//$logger = new Zend_Log($writer);
			
   			$bookingsModel = new Bookings;
   			$lineItemsModel = new LineItems;
   			$editId = $this->getRequest()->getParam('id');
   			if($this->getRequest()->isPost()){
   				$data = $this->getRequest()->getPost();
   				if($editId > 0 && trim($data['change_reason'] == '')){
   					$this->view->errors = array('You must provide a reason for the change.');
   				}
   				
   				if(trim($data['client_id']) == ''){
   					$this->view->errors = array('Please choose a client from the drop down');
   				}
   				if(trim($data['media_id']) == ''){
   					$this->view->errors = array('Please choose a publisher from the drop down');
   				}
				
				if(trim($data['circulation_rate_base']) == ''){
   					$this->view->errors = array('Please Enter Circulation Rate');
   				}
				
				//$logger->info($data['client_primary_contact']);
				
				if(($data['media_primary_contact']) == 'Select'){
   					$this->view->errors = array('Please Select Media Primary Contact');
   				}
				
				if(($data['client_primary_contact']) == 'Select'){
   					$this->view->errors = array('Please Select Client Primary Contact');
   				}

				$tmpData = explode(":", $data['client_selected_contact']);
				$data['client_primary_contact'] = $tmpData[1];
				
				$tmpData = explode(":", $data['media_selected_contact']);
				$data['media_primary_contact'] = $tmpData[1];
				
   				$orderData = array();
   				$lineItems = array();
   				
   				$lineCols = $lineItemsModel->info(Zend_Db_Table_Abstract::COLS);
   				$i=1;
   				$lineItems = array();
   				$orderData['status'] = 'Approved';
   				$orderTotal = 0;
   				while($i < 13){
   					$lineItem = array();
					//$a = trim($data["ad_spec_color_".$i]);
   					//$logger->info(isset($a));
					
					
	   				foreach($lineCols AS $col){
						
	   					if(isset($data[$col."_".$i]) && $data[$col."_".$i] <> ''){
							$lineItem[$col] = $data[$col."_".$i];
	   					}
	   					if($col == 'sales_rep'){
   							$lineItem[$col] = $this->_me->id;
   						}
   						
	   				}
	   				if(!$this->view->errors && $data['issue_cover_'.$i] <> ''){
	   				
						if($data["ad_spec_color_".$i] == '' || $data["ad_spec_bleed_".$i] == '' 
							|| $data["ad_spec_size_".$i] == '' || $data["ad_spec_position_".$i] == ''){
							//$logger->info('ad spec is empty');
							$this->view->errors = array('Please Select Ad_Spec for column no '.$i);
							break;
						}
						
						//Rate card
						if($data["rate_card_".$i] == ''){
							$this->view->errors = array('Please Add Rate Card for column no '.$i);
							break;
						}
						
						//payment due
						if($data["payment_due_".$i] == ''){
							$this->view->errors = array('Please Add Payment due for column no '.$i);
							break;
						}
						
						$lineItems[] = $lineItem;
						if((($lineItem['payment_amount'] / $lineItem['mbi_rate'])-1) < .15){
							$orderData['status'] = 'Pending';
						}
						$orderTotal = $orderTotal + $lineItem['payment_amount'];
								
	   				}
	   				
	   				$i++;
   				}
				
				$settingsModel = new Settings;
				$level_commissions = (array) $settingsModel->getLevelCommissions();
   				if($orderTotal < $level_commissions['min_total']){
   					$orderData['status'] = 'Pending';
   				}
   				if(empty($lineItems) && !$this->view->errors){
   					$this->view->errors = array("You didn't enter any line items.");
   				}
   				
   				$cols = $bookingsModel->info(Zend_Db_Table_Abstract::COLS);
   				foreach($cols AS $column){
   					if($column <> 'id' && $column <> 'sales_rep' && $column <>'marketing_rep'){
						
   						if(isset($data[$column])){
   							$orderData[$column] = $data[$column];
   						} else {
   							if($column <> 'status'){
   								$orderData[$column] = '';
   							}
   						}
   					} 
   					if($column == 'sales_rep'){
   						$orderData[$column] = $this->_me->id;
   					}
					if($column == 'marketing_rep'){
   						$orderData[$column] = $this->_me->id;
   					}
					if($column == 'revision'){
   						$orderData[$column] = 0;
   					}
   					
					
   				}
				
   				if($data['orderType'] == 'Provisional'){
   					$orderData['status'] = 'Provisional';
   				}
   				unset($data['orderType']);
   				if(!$this->view->errors){
   					try{
	   					if($editId == 0){
	   						$orderid = $bookingsModel->insert($orderData);
	   					} else {
	   						$booking = $bookingsModel->getById($editId);
	   						
	   						if($booking->orig_id <> 0){
								$orderData['orig_id'] = $booking->orig_id;
								$latestRevision = $bookingsModel->getLatestRevision($booking->orig_id);
	   						} else {
								$orderData['orig_id'] = $booking->id;
								$latestRevision = 0;
	   						}
	   						$orderData['revision'] = $latestRevision + 1;
	   						$orderid = $bookingsModel->insert($orderData);
	   					
	   					}
   					} catch (Exception $e){
   						$this->view->errors = array($e);
   					}
   					
   					
   				}
   				
   				if(!$this->view->errors){
   					foreach($lineItems AS $lineItem){
   						//if mbi rate and payment amount are zero then set payment due to nothing
   						if(($lineItem["mbi_rate"] <= 0 || empty($lineItem["mbi_rate"])) && ($lineItem["payment_amount"] <= 0 || empty($lineItem["payment_amount"]))){
   							unset($lineItem["payment_due"]);
   							$lineItem["payment_due"]='';
   						}
   							
   							$lineItem['order_id'] = $orderid;
   							$lineItemsModel->insert($lineItem);
   					
   					}
   				}
				
   				$emailModel = new Email;
   				if(!$this->view->errors){
   				//EMAIL TO BEN  IF NOT APPROVED
   					if($orderData['status'] == 'Pending'){
   						$options = array();
   						$options['subject'] = "An booking is Pending";
   						//BEN'S EMAIL ADDRESS
   						$options['to'] = 'bjohnston@media-brokers.com';
   						$options['message'] = "<img src='http://".$_SERVER['HTTP_HOST']."/images/MBIlogo.jpg'><hr>
   						<h1>A Booking is Pending</h1>
   						The IO and PO of the booking are attached. <br><br>
   						<a href='http://".$_SERVER['HTTP_HOST']."?goto=/view-booking/".$orderid."'>Click Here to Approve or Deny Order</a>";
   						
   						$options['attachments'] = array("http://".$_SERVER['HTTP_HOST']."/pdf-gen/io/".$orderid, "http://".$_SERVER['HTTP_HOST']."/pdf-gen/po/".$orderid);
   						$emailModel->sendMail($options);
   					}
   					
   					//EMAIL TO PUBLISHER AND CLIENT IF APPROVED
   					if($orderData['status'] == 'Approved'){
   						$options = array();
   						//EMAIL TO CLIENT
   						$options['subject'] = "Your Booking Has Been Submitted";
   						//CLIENT'S EMAIL ADDRESS
   						$options['to'] = $orderData['client_email'];
   						$options['message'] = "<img src='http://".$_SERVER['HTTP_HOST']."/images/MBIlogo.jpg'><hr>
   						<h1>Your Booking has Been Submitted</h1>
   						The Insertion Order of the booking is attached. Please sign and return to ".$this->_me->email.".<br><br>
   						Thank You,<br>".$this->_me->name;
   						
   						$options['attachments'] = array("http://".$_SERVER['HTTP_HOST']."/pdf-gen/io/".$orderid);
   						$emailModel->sendMail($options);
   						
   						$options = array();
   						//EMAIL TO PUBLISHER
   						$options['subject'] = "A Booking has Been Submitted";
   						//PUBLISHER'S EMAIL ADDRESS
   						$options['to'] = $orderData['media_email'];
   						$options['message'] = "<img src='http://".$_SERVER['HTTP_HOST']."/images/MBIlogo.jpg'><hr>
   						<h1>A Booking has Been Submitted</h1>
   						The Purchase Order of the booking is attached. Please sign and return to ".$this->_me->email.".<br><br>
   						Thank You,<br>".$this->_me->name;
   						
   						$options['attachments'] = array("http://".$_SERVER['HTTP_HOST']."/pdf-gen/po/".$orderid);
   						$emailModel->sendMail($options);
   					}
   					
   					$this->_redirect("/bookings");
   				}
   	
   				$this->view->data = $data;
   			}
			
   			if($editId > 0){
   				$booking = $bookingsModel->getById($editId);
   				$realData = array();
   				foreach($booking AS $key=>$value){
   					$realData[$key] = $value;
   				}
   				$lineItems = $lineItemsModel->getAllByOrder($editId);
   				$i=1;
   				foreach($lineItems AS $lines){
	   				foreach($lines AS $key => $value){
	   					$realData[$key."_".$i] = $value;
	   				
					}
					
					$realData["ad_spec_".$i] = 'View/Edit';
					
	   				$i++;
   				}
   			
   				$this->view->data = $realData;
 				$this->view->editMode = 1;
   			}
   			$usersModel= new Users;
    	$this->view->users = $usersModel->getAll();
        
    }
    
    
    public function ajaxAction(){
    	$this->_helper->layout()->disableLayout(); 
        $this->_helper->viewRenderer->setNoRender(true);
        if(!$this->_me){
    		$this->_redirect("/");
    	}
    	
    	$method = $this->getRequest()->getParam('method');
    	$term = $this->getRequest()->getParam('term');
    	
    	if($method == 'client'){
    		$objectModel = new Clients;
	$contactsModel = new ClientContacts;
    	}
    	if($method == 'publisher'){
    		$objectModel = new Publishers;
			$contactsModel = new PublisherContacts;
    	}
    	if($this->_me->type == 9 || $method == 'publisher'){
    	$objects = $objectModel->getByTerm($term);
    	} else {
    	$objects = $objectModel->getByTermAndUser($term,$this->_me->id);
    	}
    	$result = array();
    	$i = 0;
    	foreach($objects AS $object){
    		$result[$i]['value'] = $object->company;
    		foreach($object AS $key => $value){
    			$result[$i][$key] = $value;
			if($key == 'id')
				{
					if($method == 'client')
						$contacts = $contactsModel->getByClientId($value);
					else if($method == 'publisher')
						$contacts = $contactsModel->getByPublisherId($value);
					$contactList = '';
					foreach($contacts as $contact){
						$contactList .= $contact['id'].':'.$contact['name'].':'.$contact['phone'].':'.$contact['email'].':'.$contact['phone_ext'].':'.$contact['fax'].',';
					}	
				}
    		}
    		
		
			$result[$i]['contacts'] = $contactList;
    		$i++;
    	}
    	echo json_encode($result);
    }

    
}
