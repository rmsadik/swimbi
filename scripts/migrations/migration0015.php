<?
class Migration0015 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN markup varchar(255)";
		$this->query($sql);



	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

