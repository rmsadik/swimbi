<?
class Migration0020 extends Settings{

	public function up(){
		
		$sql = "CREATE TABLE IF NOT EXISTS `attachments` (
		  `id` int(6) NOT NULL AUTO_INCREMENT,
		  `date_created` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
		  `booking` varchar(255),
		  `filename` varchar(255),
		  `title` varchar(255),

		  PRIMARY KEY (`id`)
		)";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		//$sql = "DROP TABLE bookings";
	//	$this->query($sql);
		
	}

}


