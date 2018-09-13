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

$container[ 'repo' ] = function ($c) {
    return new \Attend\ClassroomsRepository($c->get('db'));
};


$app->get('/api/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
});


$app->get('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $results = $this->get('repo')->selectOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    $results = $this->get('repo')->select();
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->post('/api/classrooms', function (Request $request, Response $response, array $args) {
    $id = $this->get('repo')->insert($request->getParsedBody());
    $response->getBody()->write(json_encode([
        'status' => 'success',
        'id'     => $id
    ]));
});

$app->put('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $this->get('repo')->updateOne($args[ 'id' ], $request->getParsedBody());
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->delete('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $this->get('repo')->deleteOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->run();
