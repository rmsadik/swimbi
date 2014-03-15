<?
class Migration0007 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE line_items ADD COLUMN order_id varchar(255)";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN sales_rep varchar(255)";
		$this->query($sql);
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


