<?

$commands[] = array('command'=>'buildModel [dbtablename]','description'=>'Generate a Model Class from a database table name');


function buildModel($dbTableName=false){
	if(!$dbTableName){
		makeRed('Please provide a database table name for your model.');
		die("\n");
	}
	//Convert underscores
	$modelName = str_replace("_"," ",$dbTableName);
	//CamelCase
	$modelName = ucwords($modelName);
	//Remove Spaces
	$modelName = str_replace(" ","",$modelName);
	if(file_exists(APPLICATION_PATH."/models/".$modelName.".php")){
		die("This model already exists!!\n");
	}
	
$modelText = '<?php

class '.$modelName.' extends Zend_DB_Table
{
	protected $_name = "'.$dbTableName.'";


     /**
     * Get record by ID
     * @param type $id
     * @return type object
     */
    public function getById($id){
        $select = $this->select()
                        ->from($this->_name)
                        ->where(\'id=?\',$id);
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
';
	
	echo "\n\n Building Class $modelName for $dbTableName...\n\n";
	
	file_put_contents(APPLICATION_PATH."/models/".$modelName.".php",$modelText);
	echo "Done!\n";
}
