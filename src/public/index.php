<?php

use Slim\Container;
use Slim\App;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

/********************************************************************************
 * Main Script
 ********************************************************************************/

require('../lib/bootstrap.php');
$config = bootstrap();

$dependencies = new Container([
    'settings' => $config
]);
$app          = new App(new $dependencies);

$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new Twig_Loader_Filesystem('../templates');
    $twig   = new Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('index.html.twig', []));

    return $response;
});

$app->get('/enrollment', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new Twig_Loader_Filesystem('../templates');
    $twig   = new Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('enrollment.html.twig', []));

    return $response;
});

$app->get('/classrooms', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new Twig_Loader_Filesystem('../templates');
    $twig   = new Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('classrooms.html.twig', []));

    return $response;
});

$app->get('/api/classrooms', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $query      = new \Attend\Database\ClassroomQuery();
    $classrooms = $query->find();
    $response   = $response->withHeader('Content-type', 'application/json');
    $response->getBody()->write($classrooms->toJSON());
    return $response;
});

$app->get('/api/schedules', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $query     = new \Attend\Database\ScheduleQuery();
    $schedules = $query->find();
    $response  = $response->withHeader('Content-type', 'application/json');
    $response->getBody()->write($schedules->toJSON());

    return $response;
});

$app->get('/api/students', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $query    = new \Attend\Database\StudentQuery();
    $students = $query->find();
    $response = $response->withHeader('Content-type', 'application/json');
    $response->getBody()->write($students->toJSON());

    return $response;
});

$app->run();

