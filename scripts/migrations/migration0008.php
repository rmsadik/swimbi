<?
class Migration0008 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN disposition_notes text";
		$this->query($sql);
		$sql = "ALTER TABLE bookings ADD COLUMN placement_notes text";
		$this->query($sql);
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


