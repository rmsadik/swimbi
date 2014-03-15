<?php

class Bookings extends Zend_DB_Table
{
	protected $_name = "bookings";


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
    
    public function getStatuses(){
        $select = $this->select()
                        ->from($this->_name)
                        ->where("status <> ''")
                        ->group('status');
        $result = $this->fetchAll($select);
        $statuses = array();
        foreach($result AS $r){
        	$statuses[] = $r->status;
        }
        return $statuses;
    }
    
     /**
     * Get All Records
     * @return type object
     */
    public function getAll($status=false){
        $select = $this->select()
                        ->from($this->_name)
                        ->order('id desc');
                        if($status){
                        	$select->where('status =?',$status);
                        }
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getAllByUser($userid,$status=false){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('sales_rep=? OR split_agreement=?',$userid)
                        ->order('id desc');
                        if($status){
                        	$select->where('status =?',$status);
                        }
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getRelated($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('id=? OR orig_id=?',$id)
                        ->order('id desc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getLatestRevision($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('orig_id = '.$id)
                        ->order('revision asc');
        $results = $this->fetchAll($select);
       	$revision = 0;
       	foreach($results AS $result){
       		$revision = $result->revision;
       	}
       	return $revision;
    }
    
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
    
}
