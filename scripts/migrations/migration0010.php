<?
class Migration0010 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE publishers ADD COLUMN media_email varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE clients ADD COLUMN media_email varchar(255)";
		$this->query($sql);
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


