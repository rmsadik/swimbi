<?
class Migration0016 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE clients ADD COLUMN user varchar(255)";
		$this->query($sql);



	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

