<?

$commands[] = array('command'=>'buildController [name]','description'=>'Generate a Controller');


function buildController($name=false){
	if(!$name){
		makeRed('Please provide a name for your controller.');
		die("\n");
	}
	//Convert underscores
	$controllerName = str_replace("_","",$name);
	//CamelCase
	$controllerName = ucwords($controllerName);
	//Remove Spaces
	$controllerName = str_replace(" ","",$controllerName);
	if(file_exists(APPLICATION_PATH."/controllers/".$controllerName."Contoller.php")){
		die("This controller already exists!!\n");
	}
	
$controllerText = '<?php
class '.$controllerName.'Controller extends Swift_Controller_Action
{

    public function init(){
      
    }

	/**
	* Page
	*/
    public function indexAction(){
   
        
    }
    

    
}
';
	
	echo "\n\n Building Class $controllerName...\n\n";
	
	file_put_contents(APPLICATION_PATH."/controllers/".$controllerName."Controller.php",$controllerText);
	echo "Done!\n";
}
