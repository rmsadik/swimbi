<?
class Migration0021 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE attachments ADD COLUMN `type` varchar(255)";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


