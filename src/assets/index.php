<?php

function my_autoload($className) {

    $slash = "\\";
    $className = ltrim($className, $slash);
    $fileName  = '';
    $namespace = '';
    if ($lastNsPos = strripos($className, $slash)) {
        $namespace = substr($className, 0, $lastNsPos);
        $className = substr($className, $lastNsPos + 1);
        $fileName  = str_replace($slash, DIRECTORY_SEPARATOR, $namespace) . DIRECTORY_SEPARATOR;
    }
    $fileName .= str_replace('_', DIRECTORY_SEPARATOR, $className) . '.php';

    $require = INSTALL . DIRECTORY_SEPARATOR . 'classes' . DIRECTORY_SEPARATOR . $fileName;
    require $require;
}


/********************************************************************************
 * Main Script
 ********************************************************************************/

define( 'INSTALL', dirname( __DIR__ ));

ini_set( 'error_log', INSTALL . '/logs/php_errors.log' );
$config = parse_ini_file('../config.ini', true);
$webroot = $config['app']['root'];

spl_autoload_register( 'my_autoload' );
session_save_path( INSTALL . '/sessions');
session_start( );

// Routing: explode the incoming URI along the slashes and process accordingly
$uri = $_SERVER[ 'REQUEST_URI' ];
$uri = substr( $uri, strlen( $webroot ));
$temp = explode( '?', $uri );
$path = $temp[0];
if ( count($temp)> 1 ) {
    $t2 = explode( '&', $temp[1] );
    foreach ( $t2 as $t3 ) {
        list($k, $v) = explode( '=', $t3 );
        $params[ $k ] = $v;
    }
} else {
    $qstring = [];
    $params = [];
}
$routes = explode( '/', $path );
$route = array_shift( $routes );

switch ($route) {
    case '':
        readfile('index.html');
        break;

    case 'css':
        header('Content-Type: text/css');
        header('Content-Length: ' . filesize($route . '/' . $routes[0]));
        readfile($route . '/' . join( '/', $routes ));
        exit;

    case 'js':
        header('Content-Type: application/javascript');
        header('Content-Length: ' . filesize($route . '/' . $routes[0]));
        readfile($route . '/' . $routes[0]);
        exit;

    case 'img':
        header('Content-Type: image/png');
        header('Content-Length: ' . filesize($route . '/' . $routes[0]));
        readfile($route . '/' . $routes[0]);
        exit;


    case 'fonts':
        readfile($route . '/' . $routes[0]);
        exit;

    case 'api':
        $api = new Api();
        switch ($routes[0]) {
            case 'fetchStudents' :
                try {
                    $students = $api->fetchStudents();
                    foreach ( $students as &$student ) {
                        if ( array_key_exists( 'schedules', $_POST ) ) {
                            $student['schedules'] = $api->fetchSchedules( intval( $student['id'] ) );
                        }
                        if ( array_key_exists( 'attendance', $_POST ) ) {
                            $student['attendance'] = $api->fetchAttendance( intval( $student['id'] ) );
                        }
                    }

                    echo json_encode( [ 'success'  => TRUE,
                                        'students' => $students
                    ] );
                } catch (Exception $e) {
                    die(json_encode(['success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'fetchStudent' :
                try {
                    $student = $api->fetchStudent(intval($_POST['id']));
                    if ( array_key_exists('schedules', $_POST['schedules'])) {
                        $student['schedules'] = $api->fetchSchedules(intval($_POST['id']));
                    }
                    if ( array_key_exists( 'attendance', $_POST )) {
                        $student['attendance'] = $api->fetchAttendance(intval($_POST['id']));
                    }
                    echo json_encode([ 'success' => true, 'student' => $student]);
                } catch (Exception $e) {
                    die( json_encode([ 'success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'enrollStudent' :
                try {
                    if (!array_key_exists('familyName', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "familyName"'
                        ]));
                    }
                    if (!array_key_exists('firstName', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "firstName"'
                        ]));
                    }

                    if (!array_key_exists( 'classroomId', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "classroom"'
                        ]));
                    }
                    $student = $api->submitStudent([
                        'familyName' => $_POST['familyName'],
                        'firstName' => $_POST['firstName'],
                        'enrolled' => $_POST['enrolled'] == "true" ? 1 : 0,
                        'classroomId' => intval($_POST['classroomId']) ? intval($_POST['classroomId']) : null
                    ]);
                    $student['schedules'][] = $api->submitSchedule($student['id'], $_POST);
                    if ( $_POST['endDate']) {
                        $student['schedules'][] = $api->submitSchedule($student['id'], [
                            'schedule' => '', 'startDate' => $_POST['endDate']
                        ]);
                    }

                    echo json_encode([ 'success' => true, 'student' => $student ]);
                } catch (Exception $e) {
                    die( json_encode([ 'success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'updateStudent' :
                try {
                    if (!array_key_exists('id', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "id"'
                        ]));
                    }
                    if (!array_key_exists('familyName', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "familyName"'
                        ]));
                    }
                    if (!array_key_exists('firstName', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "firstName"'
                        ]));
                    }

                    if (!array_key_exists( 'classroomId', $_POST)) {
                        die(json_encode([
                            'success' => false,
                            'message' => 'Missing required parameter "classroom"'
                        ]));
                    }

                    $student = $api->updateStudent([
                        'id' => $_POST['id'],
                        'familyName' => $_POST['familyName'],
                        'firstName' => $_POST['firstName'],
                        'enrolled' => $_POST['enrolled'] == "true" ? 1 : 0,
                        'classroomId' => intval($_POST['classroomId']) ? intval($_POST['classroomId']) : null
                    ]);

                    if ( array_key_exists('schedule', $_POST)) {
                        $api->submitSchedule($student['id'], $_POST);

                        if ( $_POST['endDate']) {
                            $api->submitSchedule($student['id'], [
                                'schedule' => '', 'startDate' => $_POST['endDate']
                            ]);
                        }
                    }
                    $student['schedules'] = $api->fetchSchedules(intval($student['id']));
                    $student['attendance'] = null;

                    echo json_encode([ 'success' => true, 'student' => $student]);
                } catch (Exception $e) {
                    die( json_encode([ 'success' => false, 'message' => $e->getMessage( )]));
                }
                break;

            case 'deleteStudent' :
                try {
                    if (!array_key_exists('id', $_POST)) {
                        die(json_encode([
                            'success' => FALSE,
                            'message' => 'Missing required parameter "id"'
                        ]));
                    }
                    $api->deleteStudent(intval($_POST['id']));

                    echo json_encode(['success' => TRUE, 'id' => $_POST['id']]);
                } catch (Exception $e) {
                    die( json_encode([ 'success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'submitClassroom' :
                try {
                    if ( ! array_key_exists( 'name', $_POST )) {
                        die(json_encode([ 'success' => false, 'message' => 'Missing required parameter: name' ]));
                    }
                    $classroom = $api->submitClassroom([
                        $_POST['name']
                    ]);
                    echo json_encode([ 'success' => true, 'classroom' => $classroom]);
                } catch (Exception $e) {
                    die( json_encode([ 'success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'fetchClassrooms' :
                try {
                    $classrooms = $api->fetchClassrooms();
                    echo json_encode([ 'success' => true, 'classrooms' => $classrooms]);
                } catch (Exception $e) {
                    die(json_encode(['success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'checkIn' :
                try {
                    if ( false === array_key_exists( 'studentId', $_POST )) {
                        die(json_encode(['success' => false, 'message' => 'Missing required parameter "studentId"' ]));
                    }
                    if ( false === array_key_exists( 'time', $_POST )) {
                        die(json_encode(['success' => false, 'message' => 'Missing required parameter "time"' ]));
                    }
                    $attendance = $api->checkIn($_POST['studentId'], $_POST['time']);
                    echo json_encode([ 'success' => true, 'attendance' => $attendance]);
                } catch (Exception $e) {
                    die(json_encode(['success' => false, 'message' => $e->getMessage()]));
                }
                break;

            case 'checkOut' :
                try {
                    if ( false === array_key_exists( 'studentId', $_POST )) {
                        die(json_encode(['success' => false, 'message' => 'Missing required parameter "studentId"' ]));
                    }
                    if ( false === array_key_exists( 'time', $_POST )) {
                        die(json_encode(['success' => false, 'message' => 'Missing required parameter "time"' ]));
                    }
                    $attendance = $api->checkOut($_POST['studentId'], $_POST['time']);
                    echo json_encode([ 'success' => true, 'attendance' => $attendance]);
                } catch (Exception $e) {
                    die(json_encode(['success' => false, 'message' => $e->getMessage()]));
                }
                break;


            case 'login': require(INSTALL . '/api/login.php'); exit;
            case 'fetchUsers': require(INSTALL . '/api/fetchUsers.php'); exit;
            case 'addUser': require(INSTALL . '/api/addUser.php'); exit;
            case 'deleteUser': require(INSTALL . '/api/deleteUser.php'); exit;
            case 'logout' : require(INSTALL . '/api/logout.php'); exit;
            default:
//                $page = new \Pages\Error404Page();
//                render( $page, \Entities\User::getUser());
                break;
        }
        exit;

    case 'unauthorized':
//        $page = new \Pages\Error403Page();
//        $this->render( $page, \Entities\User::getUser());
        break;

    case 'pdf' :
        define('FPDF_FONTPATH', INSTALL . DIRECTORY_SEPARATOR. 'font' . DIRECTORY_SEPARATOR );
        switch ($routes[0]) {
            case 'attendance' :
                $pdf = new AttendancePdf(new Api());

                break;

            case 'signin' :
                $pdf = new SigninPdf(new Api());
                break;
        }
        $pdf->setWeekOf( $params['week'] );
        $pdf->Output();

        break;

    default:
//        $page = new \Pages\Error404Page();
//        $this->render( $page, \Entities\User::getUser());
        break;
}

