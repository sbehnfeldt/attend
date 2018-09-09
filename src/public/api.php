<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


include '../lib/bootstrap.php';

$app = new \Slim\App();

$app->get('/api/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write('OK');
});


$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    $host     = 'localhost';
    $dbname   = 'attend';
    $charset  = 'utf8mb4';
    $user     = 'attend';
    $password = 'attend';

    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=$charset", $user, $password, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    $sql = 'SELECT * FROM classrooms';
    $sth = $pdo->prepare( $sql );
    $b = $sth->execute();
    $results = $sth->fetchAll();


    $response->getBody()->write(json_encode([
        'data' => $results
    ]));


    return $response;
});

$app->run();
