<?php

namespace Attend;

require_once '../lib/bootstrap.php';
$config = bootstrap();
unset($_SESSION['account']);
header('Location: /attend');

