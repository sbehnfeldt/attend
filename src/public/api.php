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
    $results  = $this->get('classroomsRepo')->selectOne($args[ 'id' ]);
    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($results[ 0 ]));
    return $response;
});

$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    $results  = $this->get('classroomsRepo')->select();
    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($results));
    return $response;
});

$app->post('/api/classrooms', function (Request $request, Response $response, array $args) {
    $id       = $this->get('classroomsRepo')->insert($request->getParsedBody());
    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($id));

    return $response;
});

$app->put('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $this->get('classroomsRepo')->updateOne($args[ 'id' ], $request->getParsedBody());
    $response = $response->withStatus(204, 'No Content');

    return $response;
});

$app->delete('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $this->get('classroomsRepo')->deleteOne($args[ 'id' ]);
    $response = $response->withStatus(204, 'No Content');

    return $response;
});

$app->get('/api/students/{id}', function (Request $request, Response $response, array $args) {
    $results  = $this->get('studentsRepo')->selectOne($args[ 'id' ]);
    $response = $response->withStatus(200);
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($results[ 0 ]));
    return $response;
});

$app->get('/api/students', function (Request $request, Response $response, array $args) {
    $results  = $this->get('studentsRepo')->select();
    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($results));
    return $response;
});

$app->post('/api/students', function (Request $request, Response $response, array $args) {
    $id       = $this->get('studentsRepo')->insert($request->getParsedBody());
    $response = $response->withStatus(200, 'OK');
    $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($id));

    return $response;
});

$app->put('/api/students/{id}', function (Request $request, Response $response, array $args) {
    $this->get('studentsRepo')->updateOne($args[ 'id' ], $request->getParsedBody());
    $response = $response->withStatus(204, 'No Content');

    return $response;
});

$app->delete('/api/students/{id}', function (Request $request, Response $response, array $args) {
    $this->get('studentsRepo')->deleteOne($args[ 'id' ]);
    $response = $response->withStatus(204, 'No Content');

    return $response;
});

$app->run();
