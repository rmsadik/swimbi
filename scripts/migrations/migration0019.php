<?
class Migration0019 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN change_reason text";
		$this->query($sql);



	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

