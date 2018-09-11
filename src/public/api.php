<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

include '../lib/bootstrap.php';

$app                = new \Slim\App(['settings' => $config]);
$container          = $app->getContainer();
$container[ 'pdo' ] = function ($c) {
    $db  = $c[ 'settings' ][ 'db' ];
    $pdo = new PDO('mysql:host=' . $db[ 'host' ] . ';dbname=' . $db[ 'dbname' ] . ';charset=' . $db[ 'charset' ],
        $db[ 'uname' ], $db[ 'pword' ], [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);

    return $pdo;
};

$container[ 'repo' ] = function ($c) {
    $repo = new \Attend\Repository($c);

    return $repo;
};


$app->get('/api/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
});


$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    /** @var \Attend\Repository $repo */
    $repo    = $this->get('repo');
    $results = $repo->select();
    $response->getBody()->write(json_encode([
        'data' => $results
    ]));

    return $response;
});

$app->post('/api/classrooms', function (Request $request, Response $response, array $args) {
    /** @var \Attend\Repository $repo */
    $repo = $this->get('repo');
    $id   = $repo->insert($request->getParsedBody());

    $response->getBody()->write(json_encode([
        'status' => 'success',
        'id'     => $id
    ]));
});

$app->put('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {
    $parsedBody = $request->getParsedBody();
    /** @var \Attend\Repository $repo */
    $repo = $this->get('repo');
    $repo->updateOne($args[ 'id' ], $parsedBody);

    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->delete('/api/classrooms/{id}', function (Request $request, Response $response, array $args) {

    /** @var \Attend\Repository $repo */
    $repo = $this->get('repo');
    $repo->deleteOne($args[ 'id' ]);
    $response->getBody()->write(json_encode([
        'status' => 'success'
    ]));
});

$app->run();
