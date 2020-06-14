<?php

namespace Attend;


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

$app = new WebApp($dependencies);


//$app->get('/backup-db', function (Request $request, Response $response, array $args) {
//    $exporter = new Exporter();
//    $data = $exporter();
//    $data = json_encode($data);
//    $filename = "attend-db-export-" . date('Y-m-d_H-i-s') . '.json';
//
//    $response = $response->withHeader('Content-Type', 'application/octet-stream');
//    $response = $response->withHeader('Content-Disposition', 'attachment; filename="' . $filename . '"');
//    $response->getBody()->write($data);
//    return $response;
//});
//
//$app->post('/restore-db', function (Request $request, Response $response, array $args) {
//    if (empty($_FILES['restore-file']) || empty($_FILES['restore-file']['tmp_name'])) {
//        $response = $response->withStatus(400);
//        $response->getBody()->write('Missing json file');
//        return $response;
//    }
//    $json = json_decode(file_get_contents($_FILES['restore-file']['tmp_name']), true);
//
//    AccountQuery::create()->find()->delete();
//    $acctMap = [];
//    foreach ($json['accounts'] as $j) {
//        $acct = new Account();
//        $acct->setUsername($j['Username']);
//        $acct->setEmail($j['Email']);
//        $acct->setPwhash($j['Pwhash']);
//        $acct->setRole($j['Role']);
//        $acct->save();
//        $acctMap[$j['Id']] = $acct->getId();
//    }
//
//    ClassroomQuery::create()->find()->delete();
//    $classroomMap = [];
//    foreach ($json['classrooms'] as $j) {
//        $classroom = new Classroom();
//        $classroom->setLabel($j['Label']);
//        $classroom->setOrdering($j['Ordering']);
//        $classroom->setCreatedAt($j['CreatedAt']);
//        $classroom->setUpdatedAt($j['UpdatedAt']);
//        $classroom->save();
//        $classroomMap[$j['Id']] = $classroom->getId();
//    }
//
//    StudentQuery::create()->find()->delete();
//    $studentMap = [];
//    foreach ($json['students'] as $j) {
//        $student = new Student();
//        $student->setFirstName($j['FirstName']);
//        $student->setFamilyName($j['FamilyName']);
//        $student->setEnrolled($j['Enrolled']);
//        $student->setClassroomId($classroomMap[$j['ClassroomId']]);
//        $student->save();
//        $studentMap[$j['Id']] = $student->getId();
//    }
//
//    ScheduleQuery::create()->find()->delete();
//    $scheduleMap = [];
//    foreach ($json['schedules'] as $j) {
//        $schedule = new Schedule();
//        $schedule->setSchedule($j['Schedule']);
//        $schedule->setStartDate($j['StartDate']);
//        $schedule->setEnteredAt($j['EnteredAt']);
//        $schedule->setStudentId($studentMap[$j['StudentId']]);
//        $schedule->save();
//        $scheduleMap[$j['Id']] = $schedule->getId();
//    }
//
//    AttendanceQuery::create()->find()->delete();
//    $attendanceMap = [];
//    foreach ($json['attendance'] as $j) {
//        $attendance = new Attendance();
//        $attendance->setStudentId($studentMap[$j['StudentId']]);
//        $attendance->setCheckIn($j['CheckIn']);
//        $attendance->setCheckOut($j['CheckOut']);
//        $attendance->save();
//        $attendanceMap[$j['Id']] = $attendance->getId();
//    }
//
//
//    return $response;
//});


//$app->post('/profile/email', function (Request $request, Response $response) {
//    /** @var Account $account */
//    $data = [];
//    $account = $_SESSION['account'];
//    $body = $request->getParsedBody();
//
//    if (array_key_exists('email', $body)) {
//        $account->setEmail($body['email']);
//        $data['email'] = $body['email'];
//        $account->save();
//    }
//
//
//    $response = $response->withHeader('Content-Type', 'application/json');
//    $response->getBody()->write(json_encode($data));
//    return $response;
//})->add($authenticate);


//$app->post('/profile/password', function (Request $request, Response $response) {
//    $data = [];
//    $account = $_SESSION['account'];
//    $body = $request->getParsedBody();
//    if (array_key_exists('pwOld', $body) && array_key_exists('pwNew', $body)) {
//        if (!password_verify($body['pwOld'], $account->getPwhash())) {
//            $data['msg'] = 'Incorrect current password';
//        } else {
//            $account->setPwhash(password_hash($body['pwNew'], PASSWORD_DEFAULT));
//            $account->save();
//            $data['msg'] = 'OK';
//        }
//    }
//
//    $response = $response->withHeader('Content-Type', 'application/json');
//    $response->getBody()->write(json_encode($data));
//    return $response;
//})->add($authenticate);

////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for API
////////////////////////////////////////////////////////////////////////////////////////////////////

//$app->run();
$app->routes();
try {
    $app->run();
} catch (Throwable $t) {
    die("Error");
}
