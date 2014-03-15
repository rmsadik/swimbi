<?
class Migration0009 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE users ADD COLUMN commission_rate varchar(255) default 0";
		$this->query($sql);

	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


