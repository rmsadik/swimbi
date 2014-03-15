<?php

class Attachments extends Zend_DB_Table
{
	protected $_name = "attachments";


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
     * Get All Records
     * @return type object
     */
    public function getAll(){
        $select = $this->select()
                        ->from($this->_name);
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getAllByBooking($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('booking=?',$id);
        $result = $this->fetchAll($select);
        return $result;
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
