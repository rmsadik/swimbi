<?php

class Loguser extends Zend_DB_Table
{
	protected $_name = 'loguser';

    public function getUser($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('userid=?',$id);
        $result = $this->fetchRow($select);
        return $result;
    }
    
    public function getAll(){
    	$select = $this->select()
                        ->from($this->_name)
                        ->order('id asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
    public function getAllByUser($userId){
    	$select = $this->select()
                        ->from($this->_name)
                        ->where('user=?',$userId)
                        ->order('id asc');
        $result = $this->fetchAll($select);
        return $result;
    }
    
     
    public function addLogUser($input = array())
    {
    	$loguser = new Loguser;
    	$result=$loguser->insert($input);
    	
    	return $result;
    	 
    }

}

