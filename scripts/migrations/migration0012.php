<?
class Migration0012 extends Settings{

	public function up(){
		
		$sql = "ALTER TABLE line_items ADD COLUMN total_charge varchar(255) ";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN total_commission varchar(255) ";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN mbi_commission varchar(255) ";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN base_commission varchar(255) ";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN sales_bonus varchar(255) ";
		$this->query($sql);
		$sql = "ALTER TABLE line_items ADD COLUMN sales_commission varchar(255) ";
		$this->query($sql);


	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}

