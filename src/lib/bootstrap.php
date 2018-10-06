<?php

require('../vendor/autoload.php');
ini_set('display_errors', 'Off');
ini_set('error_log', '../logs/php_errors.log');
$config = parse_ini_file('../config.ini', true);

session_save_path('../sessions');
session_start();
