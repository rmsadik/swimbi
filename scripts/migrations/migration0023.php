<?
class Migration0023 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE settings ADD COLUMN `level_commissions` text";
		$this->query($sql);

	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


