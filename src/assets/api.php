<?php

use PDO;

require "bootstrap.php";


$uri = $_SERVER['REQUEST_URI'];
$uri = substr( $uri, strlen( $webroot ) );
list( $path, $qString ) = explode( '?', $uri );

$paths = explode( '/', $path );
array_shift( $paths );   // Drop the /api/ portion of the url

$resourceType = array_shift( $paths );
$resourceId   = array_shift( $paths );

$host   = $config['db']['host'];
$dbname = $config['db']['dbname'];
$uname  = $config['db']['uname'];
$pword  = $config['db']['pword'];
$pdo    = new \PDO( "mysql:host=$host;dbname=$dbname", $uname, $pword );

switch ( $resourceType ) {
	case 'classrooms':
		$repo = new ClassroomRepo( $pdo );
		break;

	case 'students':
		$repo = new StudentRepo( $pdo );
		break;

	default:
		die('Unknown resource type');
}


if ( $resourceId ) {
	$results = $repo->selectOne( $resourceId );
} else {
	$results = $repo->select();
}
echo json_encode( [ 'success' => true, 'data' => $results ] );

