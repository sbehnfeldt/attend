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

$engine   = new PropelEngine();
$host     = $config[ 'db' ][ 'host' ];
$dbname   = $config[ 'db' ][ 'dbname' ];
$user     = $config[ 'db' ][ 'uname' ];
$password = $config[ 'db' ][ 'pword' ];
$engine->connect($host, $dbname, $user, $password);

////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for Web App Pages
////////////////////////////////////////////////////////////////////////////////////////////////////
$app->get('/', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $loader = new Twig_Loader_Filesystem('../templates');
    $twig   = new Twig_Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('index.html.twig', []));

    return $response;
});

$app->get('/attendance', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
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

////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for API
////////////////////////////////////////////////////////////////////////////////////////////////////

// Classrooms
$app->get('/api/classrooms/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->getClassroomById($request, $response, $args);
    });

$app->get('/api/classrooms',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->getClassrooms($request, $response, $args);
    });

$app->post('/api/classrooms',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->postClassroom($request, $response, $args);
    });

$app->put('/api/classrooms/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->putClassroomById($request, $response, $args);
    });

$app->delete('/api/classrooms/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->deleteClassroomById($request, $response, $args);
    });


// Students
$app->get('/api/students/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->getStudentById($request, $response, $args);
    });

$app->get('/api/students',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->getStudents($request, $response, $args);
    });

$app->post('/api/students',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->postStudent($request, $response, $args);
    });

$app->put('/api/students',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->putStudentById($request, $response, $args);
    });

$app->delete('/api/students/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) use ($engine) {
        return $engine->deleteStudentById($request, $response, $args);
    });


// Schedules
$app->get('/api/schedules/{id}', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $query   = new \Attend\Database\ScheduleQuery();
    $results = $query->findPk($args[ 'id' ]);

    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-type', 'application/json');
    $response->getBody()->write($results->toJSON());

    return $response;
});

$app->get('/api/schedules', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $query   = new \Attend\Database\ScheduleQuery();
    $results = $query->find();

    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-type', 'application/json');
    $response->getBody()->write($results->toJSON());

    return $response;
});

$app->post('/api/schedules', function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
    $body     = $request->getParsedBody();
    $resource = new \Attend\Database\Schedule();
    $resource->setStartDate($body[ 'StartDate' ]);
    $resource->setSchedule($body[ 'Schedule' ]);
    $resource->setStudentId($body[ 'StudentId' ]);
    $resource->save();

    $response = $response->withStatus(201, 'Created');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($resource->getId()));

    return $response;
});

$app->delete('/api/schedules/{id}',
    function (ServerRequestInterface $request, ResponseInterface $response, array $args) {
        $query   = new \Attend\Database\ScheduleQuery();
        $results = $query->findPk($args[ 'id' ]);
        if (null === $results) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }
        $results->delete();

        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    });


$app->run();
