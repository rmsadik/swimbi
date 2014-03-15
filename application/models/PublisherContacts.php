<?php

class PublisherContacts extends Zend_DB_Table
{
	protected $_name = "publisher_contacts";


     /**
     * Get record by ID
     * @param type $id
     * @return type object
     */
    public function getById($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('id=?',$id);
        $result = $this->fetchRow($select);
        return $result;
    }
    
    
	 /**
     * Get record by ID
     * @param type $id
     * @return type object
     */
    public function getByPublisherId($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('publisher_id=?',$id);
        $result = $this->fetchAll($select);
        return $result;
    }
	
     /**
     * Get All Records
     * @return type object
     */
    public function getAll(){
        $select = $this->select()
                        ->from($this->_name);
        $result = $this->fetchAll($select);
        return $result;
    }
    
    /*public function getAllByOrder($orderid){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('order_id =?',$orderid);
        $result = $this->fetchAll($select);
        return $result;
    }*/
    
    /**
    * Update Record in Database
    * @param int $id
    * @param array $data 
    */
    function updateRecord($id,$data){
        $where =  "id = " . $id;
        $this->update($data,$where);
        return true;
    }
    
    
    /*public function isOrderRateCard($orderid){
    	$select = $this->select()
                        ->from($this->_name)
                        ->where('order_id =?',$orderid);
        $results = $this->fetchAll($select);
        $rateCard = false;
        foreach($results AS $result){
        	if($result->rate_card > 0){
        		$rateCard = true;
        	}
        }
        
        return $rateCard;
    }*/
    
}
