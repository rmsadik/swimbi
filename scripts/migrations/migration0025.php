<?
class Migration0025 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN `media_country` varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE bookings ADD COLUMN `client_country` varchar(255)";
		$this->query($sql);

	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


