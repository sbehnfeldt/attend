<?php

namespace Attend;

use Attend\Database\Account;
use Propel\Runtime\Exception\PropelException;

$username = '';
$password = '';
$email = '';
$role = '';

$acct = new Account();
$acct->setUsername($username);
$acct->setEmail($email);
$acct->setPwhash(password_hash($password, PASSWORD_BCRYPT));
$acct->setRole($role);

try {
    $acct->save();
} catch (PropelException $e) {
}
