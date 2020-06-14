<?php

namespace Attend;

use Attend\PropelEngine\PropelEngine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Container;


require '../lib/bootstrap.php';
$config = bootstrap();
$config['displayErrorDetails'] = true;
$config['determineRouteBeforeAppMiddleware'] = true;
$dependencies = new Container([
    'settings' => $config
]);
$dependencies['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, \Exception $exception) use ($c) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write('Something went wrong!');
    };
};

$dependencies['dbEngine'] = function ($c) {
    return new PropelEngine();
};

$app = new \Attend\ApiApp($dependencies);
$app->routes()->run();

