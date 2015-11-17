#!/usr/bin/env php
<?php
//  PHP versions below 5.4.0 are not supported
if (version_compare(PHP_VERSION, '5.4.0') === -1){
	echo 'This application requires at least PHP 5.4.0' . PHP_EOL;
	echo 'You are currently running ' . PHP_VERSION . '. Please update your PHP version.' . PHP_EOL;
	exit(50);
}


// symlinks are not resolved by PHP properly
// getcwd always reports source and not target
if (isset($_SERVER['PWD'])){
	define('CURRENT_DIR', $_SERVER['PWD']);
} else {
	define('CURRENT_DIR', getcwd());
}

require __DIR__ . '/app/bootstrap.php';

$application = $container['application'];
$application->run();
