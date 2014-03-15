<?


$commands[] = array('command'=>'restoreDBFromBackup','description'=>"Restore database - \033[31mWARNING! Will OVERWRITE DB!\033[37m");


function restoreDBFromBackup(){
	makeRed("Restore Database? WARNING: This will drop all tables and restore data to last backup!");
	echo "(Y/N): ";
	$data = FOPEN("php://stdin", "rb");
	$input=''; 
	WHILE (1==1) {
	   $chunk = FREAD($data, 1);
	   IF ($chunk == "\n" || $chunk == "\r") BREAK;
	   $input .= $chunk;
	}
	FCLOSE($data);
 
	if(strtolower(@$input) == 'y'){
		echo "Getting Credentials from application.ini...\n";
		$application = new Zend_Application(
		    APPLICATION_ENV,
		    APPLICATION_PATH . '/configs/application.ini'
		);
		$bootstrap = $application->getBootstrap();
	
	    $options = $bootstrap->getOptions();
	    
	    $db = $options['resources']['db']['params'];
		echo "Database Restoring. Please be patient, this could take a while...";
		sleep(1); echo "."; sleep(1); echo ".";sleep(1);echo ".";
		echo "\n";
		exec("mysql -u ".$db['username']." -p".$db['password']." ".$db['dbname']." < ".APPLICATION_PATH."/../data/dbbackup.sql",$output);
		
		makeGreen("DONE!");
		echo "\n\n";
	} else {
		echo "Operation Cancelled.\n";
	}
	

}