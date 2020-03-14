<?php

namespace Attend;


use Attend\Database\Account;
use Attend\Database\AccountQuery;
use Attend\Database\Attendance;
use Attend\Database\AttendanceQuery;
use Attend\Database\Classroom;
use Attend\Database\Exporter;
use Attend\Database\Schedule;
use Attend\Database\ScheduleQuery;
use Attend\Database\Student;
use Attend\Database\StudentQuery;
use Attend\Database\Token;
use Attend\Database\TokenQuery;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;
use Slim\Container;
use Slim\App;
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
$authenticate = function (Request $request, Response $response, $next) {
    if (empty($_SESSION['account'])) {
        // Check for "remember me" cookies, validate if found
        if (!empty($_COOKIE["account_id"]) && !empty($_COOKIE["token"])) {
            $tokens = TokenQuery::create()->filterByAccountId($_COOKIE['account_id']);
            /** @var Token $token */
            foreach ($tokens as $token) {
                if (password_verify($_COOKIE["token"], $token->getCookieHash()) && $token->getExpires() >= date("Y-m-d H:i:s", time())) {
                    $_SESSION['account'] = AccountQuery::create()->findPk($_COOKIE['account_id']);
                    break;
                }
            }
        }
    }

    if (empty($_SESSION['account'])) {
        // Not authenticated: neither by session nor "remember me" cookies
        $loader = new FilesystemLoader('../templates');
        $twig = new Environment($loader, array(
            'cache' => false
        ));
        $response->getBody()->write($twig->render('login.html.twig', [
            'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
        ]));
        return $response;
    }

    // User is authenticated
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
        $response->getBody()->write($twig->render('403.html.twig', [
            'account' => $_SESSION['account']
        ]));
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
    $response->getBody()->write($twig->render('index.html.twig', [
        'account' => $_SESSION['account'],
    ]));
    return $response;
})->add($authenticate);


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
        'account' => $_SESSION['account'],
        'classrooms' => ClassroomQuery::create()->find(),
        'weekOf' => $weekOf
    ]));
    return $response;
})->add($authenticate);


$app->get('/enrollment', function (Request $request, Response $response, array $args) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));

    $response->getBody()->write($twig->render('enrollment.html.twig', [
        'account' => $_SESSION['account'],
    ]));
    return $response;
})->add($authenticate);


$app->get('/classrooms', function (Request $request, Response $response, array $args) {
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));
    $response->getBody()->write($twig->render('classrooms.html.twig', [
        'account' => $_SESSION['account'],
    ]));
    return $response;
})->add($authenticate);

$app->get('/admin', function (Request $request, Response $response, array $args) {
    $accounts = AccountQuery::create()->find();
    $loader = new FilesystemLoader('../templates');
    $twig = new Environment($loader, array(
        'cache' => false
    ));
    $response->getBody()->write($twig->render('admin.html.twig', [
        'account' => $_SESSION['account'],
        'accounts' => $accounts
    ]));
    return $response;
})->add($authenticate)->add($adminOnly);


$app->get('/backup-db', function (Request $request, Response $response, array $args) {
    $exporter = new Exporter();
    $data = $exporter();
    $data = json_encode($data);
    $filename = "attend-db-export-" . date('Y-m-d_H-i-s') . '.json';

    $response = $response->withHeader('Content-Type', 'application/octet-stream');
    $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
    $response->getBody()->write($data);
    return $response;
});

$app->post('/restore-db', function (Request $request, Response $response, array $args) {
    if (empty($_FILES['restore-file']) || empty($_FILES['restore-file']['tmp_name'])) {
        $response = $response->withStatus(400);
        $response->getBody()->write('Missing json file');
        return $response;
    }
    $json = json_decode(file_get_contents($_FILES['restore-file']['tmp_name']), true);

    AccountQuery::create()->find()->delete();
    $acctMap = [];
    foreach ($json['accounts'] as $j) {
        $acct = new Account();
        $acct->setUsername($j['Username']);
        $acct->setEmail($j['Email']);
        $acct->setPwhash($j['Pwhash']);
        $acct->setRole($j['Role']);
        $acct->save();
        $acctMap[$j['Id']] = $acct->getId();
    }

    ClassroomQuery::create()->find()->delete();
    $classroomMap = [];
    foreach ($json['classrooms'] as $j) {
        $classroom = new Classroom();
        $classroom->setLabel($j['Label']);
        $classroom->setOrdering($j['Ordering']);
        $classroom->setCreatedAt($j['CreatedAt']);
        $classroom->setUpdatedAt($j['UpdatedAt']);
        $classroom->save();
        $classroomMap[$j['Id']] = $classroom->getId();
    }

    StudentQuery::create()->find()->delete();
    $studentMap = [];
    foreach ($json['students'] as $j) {
        $student = new Student();
        $student->setFirstName($j['FirstName']);
        $student->setFamilyName($j['FamilyName']);
        $student->setEnrolled($j['Enrolled']);
        $student->setClassroomId($classroomMap[$j['ClassroomId']]);
        $student->save();
        $studentMap[$j['Id']] = $student->getId();
    }

    ScheduleQuery::create()->find()->delete();
    $scheduleMap = [];
    foreach ($json['schedules'] as $j) {
        $schedule = new Schedule();
        $schedule->setSchedule($j['Schedule']);
        $schedule->setStartDate($j['StartDate']);
        $schedule->setEnteredAt($j['EnteredAt']);
        $schedule->setStudentId($studentMap[$j['StudentId']]);
        $schedule->save();
        $scheduleMap[$j['Id']] = $schedule->getId();
    }

    AttendanceQuery::create()->find()->delete();
    $attendanceMap = [];
    foreach ($json['attendance'] as $j) {
        $attendance = new Attendance();
        $attendance->setStudentId($studentMap[$j['StudentId']]);
        $attendance->setCheckIn($j['CheckIn']);
        $attendance->setCheckOut($j['CheckOut']);
        $attendance->save();
        $attendanceMap[$j['Id']] = $attendance->getId();
    }


    return $response;
});


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
