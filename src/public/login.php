<?php

namespace Attend;


require_once '../lib/bootstrap.php';
$config = bootstrap();
$_SESSION['account'] = 'ok';
header('Location: ' . $_GET['route']);
