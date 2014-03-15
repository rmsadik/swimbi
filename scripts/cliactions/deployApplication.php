<?

$commands[] = array('command'=>'deployApplication','description'=>'Pull in all changes on master line and deploy the new version of the site');

/**
* Run terminal commands to deploy new version of application
*/
function deployApplication(){
	//change to root directory and pull most recent code from github
	exec("cd ".APPLICATION_PATH."/../; git pull origin master", $output);
	
	//run all migrations
	exec("cd ".APPLICATION_PATH."/../scripts; php cli.php migrate", $output);
	
	//output results to console
	echo "\n\n";
	foreach($output AS $line){
		echo $line."\n";
	}
	echo "\n\n";
	
	//Toast Notification
	$toastsModel = new Toasts;
	$toastsModel->toast("MyIartz System Updated",'Success');

}