<?
class Migration0017 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings DROP COLUMN markup";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN markup varchar(255)";
		$this->query($sql);


	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

