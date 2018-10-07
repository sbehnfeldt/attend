<?php

/********************************************************************************
 * Main Script
 ********************************************************************************/

require('../lib/bootstrap.php');

$settings = $config[ 'db' ];
$pdo      = new PDO('mysql:host=' . $settings[ 'host' ] . ';dbname=' . $settings[ 'dbname' ] . ';charset=' . $settings[ 'charset' ],
    $settings[ 'uname' ], $settings[ 'pword' ], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);


$repo       = new \Attend\ClassroomsRepository($pdo);
$classrooms = $repo->select();

usort($classrooms, function ($a, $b) {
    if ($a[ 'ordering' ] > $b[ 'ordering' ]) {
        return 1;
    }
    if ($a[ 'ordering' ] < $b[ 'ordering' ]) {
        return -1;
    }

    return 0;
});

$repo     = new \Attend\StudentsRepository($pdo);
$students = [];
foreach ($classrooms as $i => $c) {
    $classrooms[ $i ][ 'students' ] = $repo->select([
        'filters' => 'classroom_id::' . $c[ 'id' ]
    ]);
    usort($classrooms[ $i ][ 'students' ], function ($a, $b) {
        if ($a[ 'family_name' ] > $b[ 'family_name' ]) {
            return 1;
        }
        if ($a[ 'family_name' ] < $b[ 'family_name' ]) {
            return -1;
        }

        if ($a[ 'first_name' ] > $b[ 'first_name' ]) {
            return 1;
        }
        if ($a[ 'first_name' ] < $b[ 'first_name' ]) {
            return -1;
        }

        return 0;
    });
}

$loader = new Twig_Loader_Filesystem('../templates');
$twig   = new Twig_Environment($loader, array(
    'cache' => false
));

echo $twig->render('index.html.twig', [
    'classrooms' => $classrooms
]);