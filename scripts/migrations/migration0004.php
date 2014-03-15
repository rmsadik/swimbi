<?
class Migration0004 extends Settings{

	public function up(){
		
		$sql = "CREATE TABLE IF NOT EXISTS `bookings` (
		  `id` int(6) NOT NULL AUTO_INCREMENT,
		  `date` varchar(255) NOT NULL,
		  `product_advertiser` varchar(255) NOT NULL,
		  `client_id` varchar(255) NOT NULL,
		  `client_company_name` varchar(255) NOT NULL,
		  `client_street_1` varchar(255) NOT NULL,
		  `client_street_2` varchar(255) NOT NULL,
		  `client_street_3` varchar(255) NOT NULL,
		  `client_city` varchar(255) NOT NULL,
		  `client_state` varchar(255) NOT NULL,
		  `client_zip` varchar(255) NOT NULL,
		  `client_phone` varchar(255) NOT NULL,
		  `client_primary_contact` varchar(255) NOT NULL,
		  `media_name` varchar(255) NOT NULL,
		  `circulation_rate_base` varchar(255) NOT NULL,
		  `ad_specs` varchar(255) NOT NULL,
		  `media_company_name` varchar(255) NOT NULL,
		  `media_street_1` varchar(255) NOT NULL,
		  `media_street_2` varchar(255) NOT NULL,
		  `media_street_3` varchar(255) NOT NULL,
		  `media_city` varchar(255) NOT NULL,
		  `media_state` varchar(255) NOT NULL,
		  `media_zip` varchar(255) NOT NULL,
		  `media_phone` varchar(255) NOT NULL,
		  `media_primary_contact` varchar(255) NOT NULL,
		  `split_agreement` varchar(255) NOT NULL,
		  `split_percent` varchar(255) NOT NULL,
		  `sales_rep` varchar(255) NOT NULL,
		  `marketing_rep` varchar(255) NOT NULL,
		  PRIMARY KEY (`id`)
		)";
		$this->query($sql);
		
	}
	
	
	public function down(){
		
		$sql = "DROP TABLE bookings";
		$this->query($sql);
		
	}

}


