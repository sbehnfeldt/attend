<?php

namespace Attend;


use Attend\Database\Account;
use Attend\Database\AccountQuery;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;
use Slim\App;
use Slim\Route;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;
use Attend\PropelEngine\PropelEngine;
use Attend\Database\ClassroomQuery;


// Find the DateTime object representing the Monday closest to the input date
function getMonday(\DateTime $d)
{
    $dw = $d->format('N');
    switch ($dw) {
        case 1:
            break;
        case 2:  // Tuesday
            $d = $d->sub(new \DateInterval('P1D'));
            break;
        case 3:  // Wednesday
            $d = $d->sub(new \DateInterval('P2D'));
            break;
        case 4:  // Thursday
            $d = $d->sub(new \DateInterval('P3D'));
            break;

        case 5:  // Friday
            $d = $d->add(new \DateInterval('P3D'));
            break;
        case 6:  // Saturday
            $d = $d->add(new \DateInterval('P2D'));
            break;
        case 7:  // Sunday
            $d = $d->add(new \DateInterval('P1D'));
            break;

        default:
            throw new \Exception(sprintf('Unknown day of the week "%d"', $dw));
            break;
    }

    return $d;
}


/********************************************************************************
 * Main Script
 ********************************************************************************/

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

$app = new App($dependencies);

$engine = new PropelEngine();
$engine->connect($config['db']);


// Middleware to ensure user is logged in
$login = function (Request $request, Response $response, $next) {
    if (empty($_SESSION['account'])) {
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader, array(
            'cache' => false
        ));

        $response->getBody()->write($twig->render('login.html.twig', [
            'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
        ]));
        return $response;
    }
    $response = $next($request, $response);
    return $response;
};


$adminOnly = function (Request $request, Response $response, $next) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));
    if (empty($_SESSION['account'])) {
        $response->getBody()->write($twig->render('login.html.twig', [
            'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
        ]));
        return $response;
    }

    if ('admin' !== $_SESSION['account']->getRole()) {
        $response = $response->withStatus(403);
        $response->getBody()->write($twig->render('403.html.twig'));
        return $response;
    }
    $response = $next($request, $response);
    return $response;
};


////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for Web App Pages
////////////////////////////////////////////////////////////////////////////////////////////////////
$app->get('/', function (Request $request, Response $response, array $args) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));
    $response->getBody()->write($twig->render('index.html.twig', []));
    return $response;
})->add($login);


$app->get('/attendance', function (Request $request, Response $response, array $args) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));

    // Get the text version of the date, suitable for column header
    $twig->addFunction(new TwigFunction('getDate', function (\DateTime $weekOf, int $d) {
        $w = new \DateTime($weekOf->format('Y/m/d'));
        $w->add(new \DateInterval(sprintf('P%dD', $d)));
        return $w->format('M j');
    }));

    $weekOf = new \DateTime('now');
    $weekOf = getMonday($weekOf);
    $response->getBody()->write($twig->render('attendance.html.twig', [
        'classrooms' => ClassroomQuery::create()->find(),
        'weekOf' => $weekOf
    ]));
    return $response;
})->add($login);


$app->get('/enrollment', function (Request $request, Response $response, array $args) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('enrollment.html.twig', []));
    return $response;
})->add($login);


$app->get('/classrooms', function (Request $request, Response $response, array $args) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));
    $response->getBody()->write($twig->render('classrooms.html.twig', []));
    return $response;
})->add($login);

$app->get('/admin', function (Request $request, Response $response, array $args) {
    $accounts = AccountQuery::create()->find();
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));
    $response->getBody()->write($twig->render('admin.html.twig', [
        'accounts' => $accounts
    ]));
    return $response;
})->add($login)->add($adminOnly);


////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for API
////////////////////////////////////////////////////////////////////////////////////////////////////

// Classrooms
$app->get('/api/classrooms/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        $resource = $engine->getClassroomById($args['id']);
        if (null === $resource) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($resource));

        return $response;
    });

$app->get('/api/classrooms',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->getClassrooms();

        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->post('/api/classrooms',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->postClassroom($request->getParsedBody());
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }

        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->put('/api/classrooms/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->putClassroomById($args['id'], $request->getParsedBody());
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->delete('/api/classrooms/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        if (!$engine->deleteClassroomById($args['id'])) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }
        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    });


// Students
$app->get('/api/students/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->getStudentById($args['id']);
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->get('/api/students',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->getStudents();
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->post('/api/students',
    function (Request $request, Response $response, array $args) use ($engine) {
        $id = $engine->postStudent($request->getParsedBody());
        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($id));

        return $response;
    });

$app->put('/api/students/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        if (!$engine->putStudentById($args['id'], $request->getParsedBody())) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($args['id']));

        return $response;
    });

$app->delete('/api/students/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        if (!$engine->deleteStudentById($args['id'])) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }
        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    });


// Schedules
$app->get('/api/schedules/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->getScheduleById($args['id']);
        if (null === $results) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->get('/api/schedules',
    function (Request $request, Response $response, array $args) use ($engine) {
        $results = $engine->getSchedules($request, $response, $args);
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write(json_encode($results));

        return $response;
    });

$app->post('/api/schedules',
    function (Request $request, Response $response, array $args) use ($engine) {
        $id = $engine->postSchedule($request->getParsedBody());
        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($id));

        return $response;
    });

$app->put('/api/schedules/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        if (!$engine->putScheduleById($args['id'], $request->getParsedBody())) {
            return $response->withStatus(404, 'Not Found');
        }
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($args['id']));

        return $response;
    });

$app->delete('/api/schedules/{id}',
    function (Request $request, Response $response, array $args) use ($engine) {
        return $engine->deleteScheduleById($request, $response, $args);
    });

$app->post('/api/accounts', function (Request $request, Response $response, array $args = []) {
    // Insert a new record into the Accounts table
    $body = $request->getParsedBody();
    $acct = new Account();
    $acct->setUsername($body['username']);
    $acct->setEmail($body['email']);
    $acct->setPwhash(password_hash($body['password'], PASSWORD_BCRYPT));
    $acct->setRole($body['role']);
    $acct->save();

    $response = $response->withStatus(201, 'Created');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($acct->getId()));

    return $response;
});

$app->put('/api/accounts/{id}', function (Request $request, Response $response, array $args = []) {
    // Update an existing record in the Accounts table
    $body = $request->getParsedBody();
    $acct = AccountQuery::create()->findPk($body['id']);

    $acct->setUsername($body['username']);
    $acct->setEmail($body['email']);
    if (!empty($body['password'])) {
        $acct->setPwhash(password_hash($body['password'], PASSWORD_BCRYPT));
    }
    $acct->setRole($body['role']);
    $acct->save();

    $response = $response->withStatus(200, 'OK');
    $response = $response->withHeader('Content-Type', 'application/json');
    $response->getBody()->write(json_encode($acct->getId()));

    return $response;
});
$app->run();
