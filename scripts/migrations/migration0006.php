<?
class Migration0006 extends Settings{

	public function up(){
		
		$sql = "CREATE TABLE IF NOT EXISTS `line_items` (
		  `id` int(6) NOT NULL AUTO_INCREMENT,
		  `issue_cover` varchar(255) NOT NULL,
		  `on_sale` varchar(255) NOT NULL,
		  `space_close` varchar(255) NOT NULL,
		  `materials_due` varchar(255) NOT NULL,
		  `rate_card` varchar(255) NOT NULL,
		  `payment_due` varchar(255) NOT NULL,
		  `payment_amount` varchar(255) NOT NULL,
		  `mbi_rate` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		)";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


