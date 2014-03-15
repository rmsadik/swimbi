<?
/**
* Site Settings and Migration Scripts
*/
class Settings extends Zend_DB_Table
{
	protected $_name = 'settings';


	/**
	* Get site's domain (needed for CLI which doesn't have $_SERVER access)
	*/
	public function domain(){
		return "http://198.61.166.201";
	}
	
	 public function getLevelCommissions(){
        $select = $this->select()
                        ->from($this->_name)
                        ->where('id > 0');
        $result = $this->fetchRow($select);
        $levels = json_decode($result['level_commissions']);
        return $levels;
    }
	
	/**
	* ==========================================
	* MIGRATION SCRIPTS - DO NOT ALTER!!!!!!!!!
	* ==========================================
	*/
	/**
	* Get current database version
	*/
	public function getVersion(){
		$exists = $this->tableExists();
		if($exists){
			$select = $this->select()
                        ->from($this->_name);
        $result = $this->fetchRow($select);
        return $result->dbversion;
		}
		
		return $version;
	}
	
	/**
	* Check whether settings table exists, if not, catch Exception and create table with db version #0
	*/
	public function tableExists(){
		try{
		 $result = $this->getDefaultAdapter()->fetchRow('SELECT * FROM '.$this->_name);
		 $result = 1;
		} catch(Exception $e) {
			$sql ="CREATE TABLE settings(
					id INT NOT NULL AUTO_INCREMENT, 
					PRIMARY KEY(id),
 					dbversion INT(20) DEFAULT 0
 					)";
 			$this->getDefaultAdapter()->query($sql);
 			$newdata['dbversion'] = 0;
 			$this->insert($newdata);
 			$result = 1;
		}
		 return $result;
	}
	
	/**
	* Run raw SQL in migration scripts
	*/ 
	public function query($sql){
		$result = $this->getDefaultAdapter()->query($sql);
		return $result;
	}

	/**
	* Update db with new version number
	*/
	public function setVersion($version){
		$newdata['dbversion'] = $version;
		$this->update($newdata,'id > 0');
	}
	
	/**
	* =============================================	
	* END MIGRATION SCRIPTS - DO NOT ALTER!!!!!!!!!
	* =============================================
	*/
}