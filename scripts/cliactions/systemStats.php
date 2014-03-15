<?

$commands[] = array('command'=>'systemStats','description'=>'Get a System Report for the Server');

/**
* Run terminal commands to output system stats in the console
*/
function systemStats(){
	exec("ifconfig eth0", $output);
	exec("head /proc/meminfo", $output);
	exec("discus", $output);
	
	echo "\n\n";
	foreach($output AS $line){
		echo $line."\n";
	}
	echo "\n\n";
}