<?php

require('../vendor/autoload.php');


function bootstrap(string $configFile = '../config.ini')
{
    ini_set('display_errors', 'Off');

    ini_set('error_log', '../logs/php_errors.log');

    if (false == ($config = parse_ini_file($configFile, true))) {
        throw new Exception(sprintf('Unable to parse config file "%s"', $configFile));
    }


    session_save_path('../sessions');
    session_start();

    return $config;
}
