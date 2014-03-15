<?php

class Publishers extends Zend_DB_Table
{
	protected $_name = "publishers";


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
                        ->where("company LIKE '%$term%'")
                        ->order('company asc');
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
