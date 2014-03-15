<?
class Migration0018 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE bookings ADD COLUMN orig_id varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE bookings ADD COLUMN revision int(11) default '0'";
		$this->query($sql);


	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

