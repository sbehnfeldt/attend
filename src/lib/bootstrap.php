<?php

require '../vendor/autoload.php';


function bootstrap(string $configFile = '../config.ini')
{
    if ( ! file_exists('../logs')) {
        if ( ! mkdir('../logs')) {
            die('Cannot make log directory');
        }
    }
    ini_set('error_log', '../logs/php_errors.log');


    if ( ! file_exists('../sessions')) {
        if ( ! mkdir('../sessions')) {
            die('Cannot make sessions directory');
        }
    }
    session_save_path('../sessions');
    if (!session_start()) {
        die('Cannot start session');
    }

    if (false == ($config = parse_ini_file($configFile, true))) {
        die(sprintf('Unable to parse config file "%s"', $configFile));
    }
    ini_set('display_errors', 'Off');


    $host = $config['db']['host'];
    $dbname = $config['db']['dbname'];
    $user = $config['db']['uname'];
    $password = $config['db']['pword'];

    $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
    $serviceContainer->checkVersion('2.0.0-dev');
    $serviceContainer->setAdapterClass('attend', 'mysql');
    $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
    $manager->setConfiguration(array(
        'classname' => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
        'dsn' => "mysql:host=$host;dbname=$dbname",
        'user' => $user,
        'password' => $password,
        'attributes' =>
            array(
                'ATTR_EMULATE_PREPARES' => false,
                'ATTR_TIMEOUT' => 30,
            ),
        'model_paths' =>
            array(
                0 => 'src',
                1 => 'vendor',
            ),
    ));
    $manager->setName('attend');
    $serviceContainer->setConnectionManager('attend', $manager);
    $serviceContainer->setDefaultDatasource('attend');

    return $config;
}
