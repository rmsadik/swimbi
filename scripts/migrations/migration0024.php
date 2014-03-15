<?
class Migration0024 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE publishers ADD COLUMN `country` varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE clients ADD COLUMN `country` varchar(255)";
		$this->query($sql);

	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


