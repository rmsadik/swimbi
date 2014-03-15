<?

$commands[] = array('command'=>'packageApplication [path/filename]','description'=>'Create an Archive of entire application');


function packageApplication($path=false){
	if(!$path){
		$path = APPLICATION_PATH."/../data/application.zip";
	}
	
	echo "Preparing to Archive Application. Please Be Patient...\n";
	if(file_exists("$path")){
		makeRed("Removing old archive...\n");
		unlink("$path");
	}
	exec("zip -9 -r $path ../*",$output);
	foreach($output AS $o){
		echo "$o\n";
	}
	makeGreen("DONE! Archive saved to ".str_replace("application/../",'',$path));
	echo "\n\n";
}