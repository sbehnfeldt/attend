<?php


use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;


include '../lib/bootstrap.php';

$app = new \Slim\App();

$app->get('/api/', function (Request $request, Response $response, array $args) {
    $response->getBody()->write( 'OK');
});


$app->get('/api/classrooms', function (Request $request, Response $response, array $args) {
    $response->getBody()->write(json_encode([
        'data' => [
            ['id' => 1, 'label' => '123s'],
            ['id' => 2, 'label' => 'ABC Jrs'],
            ['id' => 3, 'label' => 'ABCs'],
        ]
    ]));

    return $response;
});

$app->run();
