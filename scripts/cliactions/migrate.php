<?

$commands[] = array('command'=>'migrate [version#/\'version\']','description'=>'Migrate to version of database');

/**
* Used to migrate database between versions
*/
function migrate($migrateTo){
	$settingsModel = new Settings;
	(int) $currentVersion = $settingsModel->getVersion();
	
	//Display current database version
	if($migrateTo == 'version'){
		echo "\n\n";
		die("Database is currently at version #".$currentVersion."\n\n");
	}
	
	//Die if trying to migrate to current version - duh!
	if($migrateTo == $currentVersion){
		echo "\n\n";
		die("Database already at version #".$migrateTo."\n\n");
	}
	
	//Get migration scripts
	$migrations = scandir(APPLICATION_PATH."/../scripts/migrations");
	
	//Get latest migration number and filter .. and .
	foreach($migrations AS $migration){
		if($migration<> '.' && $migration <> '..'){
			(int) $number = str_replace("migration",'',str_replace(".php",'',$migration));
			
			$latestMigration = $number;
		}
	}
	
	//decide whether to take user input or migrate to most recent version
	if(isset($migrateTo)){
		(int)$migrateTo = $migrateTo;
	} else {
		(int)$migrateTo = $latestMigration;
	}
	
	//Die if trying to migrate to current version - duh!
	if($latestMigration == $currentVersion){
		echo "\n\n";
		die("Database already at version #".$migrateTo."\n\n");
	}
	
	//decide whether we are migrating up or down and order scripts accordingly
	if($migrateTo <= $currentVersion){
		$direction = 'down';
		$migrations = array_reverse($migrations);
	} else {
		$direction = 'up';
	}
	
	//build runFiles array
	$runFiles = array();
	foreach($migrations AS $migration){
		if($migration<> '.' && $migration <> '..'){
			//get only the version number
			(int) $number = str_replace("migration",'',str_replace(".php",'',$migration));
			if($direction == 'up'){
				if($currentVersion < $number && $number <= $migrateTo){
					//add to runFiles array
					$runFiles[] = $migration;
				}
			}
			if($direction == 'down'){
				if($currentVersion >= $number && $number > $migrateTo){
					//add to runFiles array
					$runFiles[] = $migration;
				}
			}
			
		}
	}
	//start counter at 0
	$migratedTo = 0;

	//cycle through scripts
	foreach($runFiles AS $file){
		//get only the version number
		(int) $number = str_replace("migration",'',str_replace(".php",'',$file));
		
		//get migration class name
		$className = ucwords(str_replace(".php",'',$file));
		
		//include dependencies
		include APPLICATION_PATH.'/../scripts/migrations/'.$file;
		
		//instantiate migration class
		$migration = new $className;
		
		if($direction == 'up'){
			//run up() migration method
			$migration->up();
			//mark as last migration run
			$migratedTo = $number;
		}
		if($direction == 'down'){
			//run down migration method
			$migration->down();
			//mark as lat migration run
			$migratedTo = $number - 1;
		}
		
		
	}
	//update db with new version number
	$settingsModel->setVersion($migratedTo);
	
	//output results to terminal
	echo "\n\n";
	echo "Database Migrated to Version #".$migratedTo;
	echo "\n\n";

}