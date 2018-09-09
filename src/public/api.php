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


$app->get('/api/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
});


$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    $pdo     = $this->get('pdo');
    $sql     = 'SELECT * FROM classrooms';
    $sth     = $pdo->prepare($sql);
    $b       = $sth->execute();
    $results = $sth->fetchAll();


    $response->getBody()->write(json_encode([
        'data' => $results
    ]));


    return $response;
});

$app->run();
