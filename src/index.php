<?php

// Retrieve instance of the framework
$f3=require('lib/base.php');

// Load configuration
$f3->config('app/config.ini');

// Define routes
$f3->config('app/routes.ini');

// Define combined variable for scheme, host, and base
$schemeHost = $f3->get('SCHEME').'://'.$f3->get('HOST').$f3->get('BASE');
$f3->set('schemeHost',$schemeHost);

ini_set('error_log',$f3->get('LOGS').'error.log');
$f3->set('ONERROR','CommonPageController->error');

// $f3->set('ONERROR',
// 	function($f3){
// 		$logger	= new Log('error.log');
// 		$code	=$f3->get('ERROR.code');
// 		$status	=$f3->get('ERROR.status');
// 		$text	=$f3->get('ERROR.text');
// 		$logger->write("[$code] - [$status] - [$text]");
// 		//$f3->reroute('/error');
// 	}
// );

$f3->run();
