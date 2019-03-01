<?php

require('../vendor/autoload.php');
require('./config.php');

function bootstrap(string $configFile = '../config.ini')
{
    ini_set('display_errors', 'Off');

    ini_set('error_log', '../logs/php_errors.log');

    $config = parse_ini_file($configFile, true);

    session_save_path('../sessions');
    session_start();

    return $config;
}
