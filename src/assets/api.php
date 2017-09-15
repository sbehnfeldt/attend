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
$pdo = new \PDO( "mysql:host=$host;dbname=$dbname", $uname, $pword );

switch ( $resourceType ) {
	case 'students':
		$repo     = new StudentRepo( $pdo );
		$students = $repo->selectOne( $resourceId );
		echo json_encode( [ 'success' => true, 'students' => $students ] );
		break;

	case 'classrooms':
		$repo       = new ClassroomRepo( $pdo );
		$classrooms = $repo->selectOne( $resourceId );
		echo json_encode( [ 'success' => true, 'classrooms' => $classrooms ] );
		break;

	default:
		break;
}




//
//
//if ( count( $temp ) > 1 ) {
//	$t2 = explode( '&', $temp[1] );
//	foreach ( $t2 as $t3 ) {
//		list( $k, $v ) = explode( '=', $t3 );
//		$params[ $k ] = $v;
//	}
//} else {
//	$qstring = [ ];
//	$params  = [ ];
//}
//$routes = explode( '/', $path );
//$route  = array_shift( $routes );
//
