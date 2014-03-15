<?
class Migration0005 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN media_id varchar(255) NOT NULL";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE publishers";
	//	$this->query($sql);
		
	}

}


