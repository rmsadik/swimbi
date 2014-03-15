<?
class Migration0022 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE publishers ADD COLUMN `circulation_rate_base` varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE clients ADD COLUMN `split_percent` varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE clients ADD COLUMN `split_agreement` varchar(255)";
		$this->query($sql);
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


