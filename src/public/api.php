<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

include '../lib/bootstrap.php';

$app       = new \Slim\App(['settings' => $config]);
$container = $app->getContainer();

$container[ 'db' ] = function ($c) {
    $settings = $c[ 'settings' ][ 'db' ];
    $pdo      = new PDO('mysql:host=' . $settings[ 'host' ] . ';dbname=' . $settings[ 'dbname' ] . ';charset=' . $settings[ 'charset' ],
        $settings[ 'uname' ], $settings[ 'pword' ], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

    return $pdo;
};

$container[ 'classroomsRepo' ] = function ($c) {
    return new \Attend\ClassroomsRepository($c->get('db'));
};

$container[ 'studentsRepo' ] = function ($c) {
    return new \Attend\StudentsRepository($c->get('db'));
};


$app->get('/api/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
});


$app->get('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $results = $this->get('classroomsRepo')->selectOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    $results = $this->get('classroomsRepo')->select();
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->post('/api/classrooms', function (Request $request, Response $response, array $args) {
    $id = $this->get('classroomsRepo')->insert($request->getParsedBody());
    $response->getBody()->write(json_encode([
        'status' => 'success',
        'id'     => $id
    ]));
});

$app->put('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $this->get('classroomsRepo')->updateOne($args[ 'id' ], $request->getParsedBody());
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->delete('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $this->get('classroomsRepo')->deleteOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->get('/api/students/{id}', function (Request $request, Response $response, array $args) {
    $results = $this->get('studentsRepo')->selectOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->get('/api/students', function (Request $request, Response $response, array $args) {
    $results = $this->get('studentsRepo')->select();
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->post('/api/students', function (Request $request, Response $response, array $args) {
    $id = $this->get('studentsRepo')->insert($request->getParsedBody());
    $response->getBody()->write(json_encode([
        'status' => 'success',
        'id'     => $id
    ]));
});

$app->put('/api/students/{id}', function (Request $request, Response $response, array $args) {
    $this->get('studentsRepo')->updateOne($args[ 'id' ], $request->getParsedBody());
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->delete('/api/students/{id}', function (Request $request, Response $response, array $args) {
    $this->get('studentsRepo')->deleteOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->run();
