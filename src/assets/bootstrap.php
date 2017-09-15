<?php
function my_autoload($className) {

	$slash = "\\";
	$className = ltrim($className, $slash);
	$fileName  = '';
	$namespace = '';
	if ($lastNsPos = strripos($className, $slash)) {
		$namespace = substr($className, 0, $lastNsPos);
		$className = substr($className, $lastNsPos + 1);
		$fileName  = str_replace($slash, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
	}
	$fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

	$require = INSTALL . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $fileName;
	require $require;
}

define( 'INSTALL', dirname( __DIR__ ));

ini_set( 'error_log', INSTALL . '/logs/php_errors.log' );
$config = parse_ini_file('../config.ini', true);
$webroot = $config['app']['root'];

spl_autoload_register( 'my_autoload' );
session_save_path( INSTALL . '/sessions');
session_start( );
