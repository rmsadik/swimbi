<?
class Migration0003 extends Settings{

	public function up(){
		
		$sql = "CREATE TABLE IF NOT EXISTS `publishers` (
		  `id` int(6) NOT NULL AUTO_INCREMENT,
		  `company` varchar(255) NOT NULL,
		  `email` varchar(255) NOT NULL,
		  `primary_contact` varchar(255) NOT NULL,
		  `name` longtext NOT NULL,
		  `phone` longtext NOT NULL,
		  `street1` longtext NOT NULL,
		  `street2` longtext NOT NULL,
		  `street3` longtext NOT NULL,
		  `city` longtext NOT NULL,
		  `state` longtext NOT NULL,
		  `zip` longtext NOT NULL,
		  `url` longtext NOT NULL,
		  PRIMARY KEY (`id`)
		)";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		$sql = "DROP TABLE publishers";
		$this->query($sql);
		
	}

}


