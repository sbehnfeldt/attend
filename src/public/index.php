<?php

/********************************************************************************
 * Main Script
 ********************************************************************************/

require( '../vendor/autoload.php' );

define('INSTALL', dirname(__DIR__));

ini_set( 'error_log', INSTALL . '/logs/php_errors.log' );
$config = parse_ini_file('../config.ini', true);
$webroot = $config['app']['root'];

session_save_path( INSTALL . '/sessions');
session_start( );

ini_set('error_log',  '../logs/php_errors.log');
$config  = parse_ini_file('../config.ini', true);
$webroot = $config[ 'app' ][ 'root' ];

$loader = new Twig_Loader_Filesystem('../templates');
$twig = new Twig_Environment($loader, array(
    'cache' => false
));

echo $twig->render('index.html.twig');