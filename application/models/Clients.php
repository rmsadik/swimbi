<?php

class Clients extends Zend_DB_Table
{
	protected $_name = "clients";


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
                        ->from($this->_name)
                        ->order('company asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getByTerm($term){
    	$select = $this->select()
                        ->from($this->_name)
                        ->where("company LIKE '%".$term."%'")
                        ->order('company asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getByTermAndUser($term,$userid){
    	$select = $this->select()
                        ->from($this->_name)
                        ->where("company LIKE '%".$term."%'")
                        ->where("user = ?",$userid)
                        ->order('company asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getAllByUser($userid){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('user=?',$userid)
                        ->order('id desc');
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
