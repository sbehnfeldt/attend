<?php

use Propel\Runtime\Connection\ConnectionManagerSingle;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Propel;

require '../vendor/autoload.php';


function getToken($length)
{
    $token        = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max          = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

function foo(Propel $serviceContainer)
{
    $serviceContainer->initDatabaseMapFromDumps(array(
        'attend' =>
            array(
                'tablesByName'    =>
                    array(
                        'accounts'               => '\\flapjack\\attend\\database\\Map\\AccountTableMap',
                        'attendance'             => '\\flapjack\\attend\\database\\Map\\AttendanceTableMap',
                        'classrooms'             => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                        'group_members'          => '\\flapjack\\attend\\database\\Map\\GroupMemberTableMap',
                        'group_permissions'      => '\\flapjack\\attend\\database\\Map\\GroupPermissionTableMap',
                        'groups'                 => '\\flapjack\\attend\\database\\Map\\GroupTableMap',
                        'individual_permissions' => '\\flapjack\\attend\\database\\Map\\IndividualPermissionTableMap',
                        'login_attempts'         => '\\flapjack\\attend\\database\\Map\\LoginAttemptTableMap',
                        'permissions'            => '\\flapjack\\attend\\database\\Map\\PermissionTableMap',
                        'schedules'              => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                        'students'               => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                        'token_auths'            => '\\flapjack\\attend\\database\\Map\\TokenAuthTableMap',
                    ),
                'tablesByPhpName' =>
                    array(
                        '\\Account'              => '\\flapjack\\attend\\database\\Map\\AccountTableMap',
                        '\\Attendance'           => '\\flapjack\\attend\\database\\Map\\AttendanceTableMap',
                        '\\Classroom'            => '\\flapjack\\attend\\database\\Map\\ClassroomTableMap',
                        '\\Group'                => '\\flapjack\\attend\\database\\Map\\GroupTableMap',
                        '\\GroupMember'          => '\\flapjack\\attend\\database\\Map\\GroupMemberTableMap',
                        '\\GroupPermission'      => '\\flapjack\\attend\\database\\Map\\GroupPermissionTableMap',
                        '\\IndividualPermission' => '\\flapjack\\attend\\database\\Map\\IndividualPermissionTableMap',
                        '\\LoginAttempt'         => '\\flapjack\\attend\\database\\Map\\LoginAttemptTableMap',
                        '\\Permission'           => '\\flapjack\\attend\\database\\Map\\PermissionTableMap',
                        '\\Schedule'             => '\\flapjack\\attend\\database\\Map\\ScheduleTableMap',
                        '\\Student'              => '\\flapjack\\attend\\database\\Map\\StudentTableMap',
                        '\\TokenAuth'            => '\\flapjack\\attend\\database\\Map\\TokenAuthTableMap',
                    ),
            ),
    ));
}


/**
 * @param  string  $configFile
 *
 * @return array|void
 * @throws PropelException
 */
function bootstrap(string $configFile = '../config.ini')
{
    // Make sure the "logs" directory exists; specify all PHP errors to be written to error log in this directory
    if ( ! file_exists('../logs')) {
        if ( ! mkdir('../logs')) {
            die('Cannot make log directory');
        }
    }
    ini_set('error_log', '../logs/php_errors.log');


    // Make sure the "sessions" directory exists; start the session
    if ( ! file_exists('../sessions')) {
        if ( ! mkdir('../sessions')) {
            die('Cannot make sessions directory');
        }
    }
    session_save_path('../sessions');
    if ( ! session_start()) {
        die('Cannot start session');
    }


    // Parse config file into associative array
    if ( ! file_exists($configFile)) {
        die(sprintf('Config file "%s" not found', $configFile));
    }
    if (false == ($config = parse_ini_file($configFile, true))) {
        die(sprintf('Unable to parse config file "%s"', $configFile));
    }


// From this point on, the application is "good to go", so no errors from here on out should be displayed on the web output
    ini_set('display_errors', 'Off');


/// Create the service container
    $serviceContainer = Propel::getServiceContainer();
//    $serviceContainer->checkVersion('2.0.0-dev');
    $serviceContainer->checkVersion(2);
    $serviceContainer->setAdapterClass('attend', 'mysql');


// Create the DB connection manager, initializing according to
    $manager = new ConnectionManagerSingle('attend');
    $manager->setConfiguration([
        'classname'   => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
        'dsn'         => "mysql:host={$config['db']['host']};dbname={$config['db']['dbname']}",
        'user'        => $config['db']['uname'],
        'password'    => $config['db']['pword'],
        'attributes'  => [
            'ATTR_EMULATE_PREPARES' => false,
            'ATTR_TIMEOUT'          => 30,
        ],
        'model_paths' => [
            0 => 'src',
            1 => 'vendor',
        ],
    ]);
    $manager->setName('attend');
    $serviceContainer->setConnectionManager($manager);
    $serviceContainer->setDefaultDatasource('attend');

    require_once 'loadDatabase.php';

    return $config;
}
