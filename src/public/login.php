<?php

namespace Attend;


use Attend\Database\AccountQuery;

require_once '../lib/bootstrap.php';
$config = bootstrap();

if (!empty($_POST['username'])) {
    $username = $_POST['username'];
}
if (!empty($_POST['password'])) {
    $password = $_POST['password'];
}

try {
    if (!$username || !$password) {
        header('Content-Type: application/json');
        die (json_encode([
            'invalid' => true,
            'username' => !empty($_POST['username']),
            'password' => !empty($_POST['password'])
        ]));
    }

    $acct = AccountQuery::create()->findOneByUsername($username);
    if (!$acct) {
        header('Content-Type: application/json');
        die (json_encode([
            'unauthorized' => true
        ]));
    }

    if (!password_verify($password, $acct->getPwhash())) {
        header('Content-Type: application/json');
        die (json_encode([
            'unauthorized' => true
        ]));
    }
} catch (\Exception $e) {
    die (json_encode([
        'unauthorized' => true
    ]));
}

$_SESSION['account'] = $acct;

header('Content-Type: application/json');
die(json_encode([
    'Location' => $_POST['route']
]));
