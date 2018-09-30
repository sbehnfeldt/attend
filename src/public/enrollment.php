<?php

require('../lib/bootstrap.php');


$loader = new Twig_Loader_Filesystem('../templates');
$twig   = new Twig_Environment($loader, array(
    'cache' => false
));

$pdo        = new PDO('mysql:host=' . $config[ 'db' ][ 'host' ] . ';dbname=' . $config[ 'db' ][ 'dbname' ] . ';charset=' . $config[ 'db' ][ 'charset' ],
    $config[ 'db' ][ 'uname' ], $config[ 'db' ][ 'pword' ], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
$repo       = new \Attend\ClassroomsRepository($pdo);
$classrooms = $repo->select();
usort($classrooms, function ($a, $b) {
    if ($a[ 'ordering' ] > $b[ 'ordering' ]) {
        return 1;
    };
    if ($a[ 'ordering' ] < $b[ 'ordering' ]) {
        return -1;
    };

    return 0;
});

echo $twig->render('enrollment.html.twig', [
    'classrooms' => $classrooms
]);
