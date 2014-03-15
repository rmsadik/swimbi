<?
class Migration0011 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE publishers DROP COLUMN media_email ";
		$this->query($sql);
		$sql = "ALTER TABLE clients DROP COLUMN media_email";
		$this->query($sql);
		$sql = "ALTER TABLE bookings ADD COLUMN media_email varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE bookings ADD COLUMN client_email varchar(255)";
		$this->query($sql);
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


