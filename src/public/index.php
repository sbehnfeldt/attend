<?php

namespace Attend;


use Attend\PropelEngine\PropelEngine;
use DateInterval;
use DateTime;
use Exception;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;
use Throwable;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


require '../lib/bootstrap.php';
$config = bootstrap();
$config['displayErrorDetails'] = true;
$config['determineRouteBeforeAppMiddleware'] = true;

$dependencies = new Container([
    'settings' => $config
]);

$dependencies['errorHandler'] = function ($c) {
    return function (Request $request, Response $response, Exception $exception) use ($c) {
        return $response->withStatus(500)
            ->withHeader('Content-Type', 'text/html')
            ->write($exception->getMessage());
    };
};

$dependencies['render'] = function ($c) {
    return function (string $template, $params) {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader, array(
            'cache' => false
        ));
        $twig->addFunction(new TwigFunction('getDate', function (DateTime $weekOf, int $d) {
            $w = new DateTime($weekOf->format('Y/m/d'));
            $w->add(new DateInterval(sprintf('P%dD', $d)));
            return $w->format('M j');
        }));
        return $twig->render($template, $params);
    };
};

$dependencies[ 'dbEngine' ] = function($c) {
    return new PropelEngine();
};

$app = new WebApp($dependencies);


$app->routes();
try {
    $app->run();
} catch (Throwable $t) {
    die("Error");
}
