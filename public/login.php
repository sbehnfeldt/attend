<?php

namespace Attend;


use Attend\Database\AccountQuery;
use Attend\Database\LoginAttempt;
use Attend\Database\Token;
use Attend\Database\TokenQuery;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once '../lib/bootstrap.php';


function getToken($length)
{
    $token = "";
    $codeAlphabet = "ABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $codeAlphabet .= "abcdefghijklmnopqrstuvwxyz";
    $codeAlphabet .= "0123456789";
    $max = strlen($codeAlphabet);

    for ($i = 0; $i < $length; $i++) {
        $token .= $codeAlphabet[random_int(0, $max - 1)];
    }

    return $token;
}

$config = bootstrap();
$logger = new Logger('logins');
$logger->pushHandler(new StreamHandler('../logs/security.log'));

if (empty($_POST['username']) || empty($_POST['password'])) {
    $note = 'Invalid login attempt';
    $attempt = new LoginAttempt();
    $attempt->setAttemptedAt(time());
    $attempt->setPass(0);
    $attempt->setNote($note);
    $attempt->save();
    $logger->info($note);

    header('Content-Type: application/json');
    die (json_encode([
        'invalid' => true,
        'username' => !empty($_POST['username']),
        'password' => !empty($_POST['password'])
    ]));
}

$username = $_POST['username'];
$password = $_POST['password'];
$acct = AccountQuery::create()->findOneByUsername($username);
if (!$acct) {
    // User not found
    $note = sprintf('Login denied: no account for user "%s"', $username);
    $attempt = new LoginAttempt();
    $attempt->setUsername($username);
    $attempt->setAttemptedAt(time());
    $attempt->setPass(0);
    $attempt->setNote($note);
    $attempt->save();
    $logger->info($note);

    header('Content-Type: application/json');
    die (json_encode([
        'unauthorized' => true
    ]));
}

if (!password_verify($password, $acct->getPwhash())) {
    // Wrong password
    $note = sprintf('Login denied: incorrect password for user "%s"', $username);
    $attempt = new LoginAttempt();
    $attempt->setUsername($username);
    $attempt->setAttemptedAt(time());
    $attempt->setPass(0);
    $attempt->setNote($note);
    $attempt->save();
    $logger->info($note);

    header('Content-Type: application/json');
    die (json_encode([
        'unauthorized' => true
    ]));
}


// User authenticated
$logger->info(sprintf('User "%s" successfully logged in', $username));
$attempt = new LoginAttempt();
$attempt->setUsername($username);
$attempt->setAttemptedAt(time());
$attempt->save();
$_SESSION['account'] = $acct;
if (empty($_POST['remember'])) {
    // Clear any current auth cookie
    setcookie("account_id", null, time() - 1);
    setcookie("token", null, time() - 1);

} else {
    // Mark any existing token as expired
    $tokens = TokenQuery::create()->findByAccountId($acct->getId());
    foreach ($tokens as $token) {
        try {
            $token->delete();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }

    $expiration = time() + (30 * 24 * 60 * 60);  // for 1 month
    setcookie("account_id", $acct->getId(), $expiration);
    $random = getToken(32);
    setcookie("token", $random, $expiration);

    $token = new Token();
    $token->setAccountId($acct->getId());
    $token->setCookieHash(password_hash($random, PASSWORD_DEFAULT));
    $token->setExpires(date("Y-m-d H:i:s", $expiration));
    try {
        $token->save();
    } catch (\Exception $e) {
        die($e->getMessage());
    }
}

header('Content-Type: application/json');
die(json_encode([
    'Location' => $_POST['route']
]));

