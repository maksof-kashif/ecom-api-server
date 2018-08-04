<?php

    define('Base_url', 'http://localhost/app/');
    define('File_path', "audio/");
    
	require_once("vendor/Paris/idiorm.php");
	require_once("vendor/Paris/paris.php");

	$ENVIROMENT = 'PRODUCTION';
	//$ENVIROMENT = 'DEVELOPMENT';

	if($ENVIROMENT == 'PRODUCTION') {
		ORM::configure('mysql:host=68.169.36.6;dbname=maksof_openprofit');
	    ORM::configure('username', 'maksof_openprofit');
	    ORM::configure('password', 'openprofit123');
	    ORM::configure('logging', true);
	} else {
		ORM::configure('mysql:host=localhost;dbname=openprofit');
	    ORM::configure('username', 'root');
	    ORM::configure('password', '');
	    ORM::configure('logging', true);		
	}

?>