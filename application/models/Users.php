<?php

class Users extends Zend_DB_Table
{
	protected $_name = 'users';

	 /**
     * Get user info
     * @param type $id
     * @return type user
     */
    public function getUser($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('id=?',$id);
        $result = $this->fetchRow($select);
        return $result;
    }
    
     /**
     * Get user info
     * @param type $username
     * @return type user
     */
    public function getUserByEmail($email){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('email=?',$email);
        $result = $this->fetchRow($select);
        return $result;
    }
    
    public function getAll(){
    	$select = $this->select()
                        ->from($this->_name)
                        ->order('name asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getAllByUser($user){
    	$select = $this->select()
                        ->from($this->_name)
                        ->where('user=?',$user)
                        ->order('name asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
     /**
     * Update user table
     * @param type $id -  id of user to be updated
     * @param type $data - array of key value pairs for table
     */
    function updateUser($id,$data){
        $where =  "id = " . $id;
        $this->update($data,$where);
        return true;
    }
 

}

