<?php 
echo "
================================================================================================================
================================================================================================================
  ___________      __.______________________________________________ _______  ________   
 /   _____/  \    /  \   \_   _____/\__    ___/\____    /\_   _____/ \      \ \______ \  
 \_____  \\   \/\/   /   ||    __)    |    |     /     /  |    __)_  /   |   \ |    |  \ 
 /        \\        /|   ||     \     |    |    /     /_  |        \/    |    \|    `   \
/_______  / \__/\  / |___|\___  /     |____|   /_______ \/_______  /\____|__  /_______  /
        \/       \/           \/                       \/        \/         \/        \/
================================================================================================================
================================================================================================================        
";	
$outputfile = "application.zip";
echo "Copying source...\n";
copy("http://www.swifteezy.com/application.zip",$outputfile);
echo "Decompressing..\n";
exec("unzip application.zip -d ."); 
exec('rm application.zip');
exec('rm install.php');
echo "\n\n\033[32mInstallation Complete! Enjoy!\033[0m\n\n";


echo"
================================================================================================================
GETTING STARTED
================================================================================================================

1. SETUP YOUR DATABASE
--------------------------------------------
Set your database parameters in application/configs/application.ini
	
	
2. ACCESS THE COMMAND LINE INTERFACE (CLI)
----------------------------------------------------------------------------------------
To access CLI tools, type 'cd scripts; php cli.php;' in the terminal.
You will be presented with a list of options.


3. GENERATE YOUR CONTROLLERS AND MODELS
----------------------------------------------------------------------------------------
You can generate controllers and models with 
the CLI tools.


4. SETUP YOUR ROUTES
----------------------------------------------------------------------------------------
Setup custom SEO URLs in the application/configs/route.ini file

5. APACHE SETUP
----------------------------------------------------------------------------------------
Apache must have mod_rewrite enabled for routing to work properly

For Debian distros:
> a2enmod rewrite
> service apache2 restart

";