<?php

namespace Attend;


require_once '../lib/bootstrap.php';
$config = bootstrap();

$accounts = [
    'david' => [
        'username' => 'david',
        'password' => 'temp-password',
        'email' => 'david.galli@camelotschool.net',
        'role' => 'user'
    ],
    'stephen' => [
        'username' => 'stephen',
        'password' => 'temp-password',
        'email' => 'stephen@behnfeldt.pro',
        'role' => 'user'
    ]
];

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

    if (!array_key_exists($username, $accounts)) {
        header('Content-Type: application/json');
        die (json_encode([
            'unauthorized' => true
        ]));
    }

    $account = $accounts[$username];
    if ($password !== $account['password']) {
        header('Content-Type: application/json');
        die (json_encode([
            'unauthorized' => true
        ]));
    }
} catch (\Exception $e) {

}

$_SESSION['account'] = $account;

header('Content-Type: application/json');
die(json_encode([
    'Location' => $_POST['route']
]));
