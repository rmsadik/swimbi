<?
class Migration0013 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN status varchar(255) DEFAULT 'Pending'";
		$this->query($sql);



	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

