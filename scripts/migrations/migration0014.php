<?
class Migration0014 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE line_items ADD COLUMN reason text";
		$this->query($sql);



	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

