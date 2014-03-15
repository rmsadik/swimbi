<?
class Migration0001 extends Settings{

	public function up(){
		
		$sql = "CREATE TABLE IF NOT EXISTS `users` (
		  `id` int(6) NOT NULL AUTO_INCREMENT,
		  `type` tinyint(1) NOT NULL DEFAULT '0',
		  `email` varchar(255) NOT NULL,
		  `name` longtext NOT NULL,
		  `phone` longtext NOT NULL,
		  `street` longtext NOT NULL,
		  `city` longtext NOT NULL,
		  `state` longtext NOT NULL,
		  `zip` longtext NOT NULL,
		  `url` longtext NOT NULL,
		  `password` varchar(200) NOT NULL,
		  PRIMARY KEY (`id`)
		)";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		$sql = "DROP TABLE users";
		$this->query($sql);
		
	}

}


