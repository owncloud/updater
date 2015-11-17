#!/usr/bin/env php
<?php
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
