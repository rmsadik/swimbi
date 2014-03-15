<?php 
require('clibootstrap.php');

$commands = array();


/** Include commands files - must be listed here to be used. **/
$actions = scandir('cliactions');

foreach($actions AS $file){
	if($file <> '.' && $file <> '..'){
		include 'cliactions/'.$file;
	}
}




//Run function or provide help
if(count($argv) > 1){
	$settingsModel = new Settings;
        $offset =  date('Z')/60/60;
        $settingsModel->query("SET time_zone = '$offset:00'");
    $argv[1](@$argv[2]);
} else {
   echo "
\033[1;33m==============================================
==============================================
   _____         _ ______  ________    ____
  / ___/      __(_) __/ /_/ ____/ /   /  _/
  \__ \ | /| / / / /_  __/ /   / /    / /  
 ___/ / |/ |/ / / __/ /_/ /___/ /____/ /   
/____/|__/|__/_/_/  \__/\____/_____/___/   
==============================================
==============================================\033[0m                                                                                     
                                                                           
Zend Framework Command Line Interface                                                                     
   
\033[1;30;32mCLI Command Line Interface for PHP \033[0m
Author: Brandon Swift 2012\n\n
To run a command, type 'php cli.php [commandName]' then hit Enter
\n\n
Available Methods: \n";
   
   
   foreach($commands AS $command){
       echo "\033[1;30;32m".$command['command'] ."\033[0m - ". $command['description']."\n";
   }
   echo "\n\n";
}


function makeRed($text){
	echo "\033[31m$text\033[0m";
}

function makeGreen($text){
	echo "\033[32m$text\033[0m";
}