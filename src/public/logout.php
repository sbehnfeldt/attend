<?php

namespace Attend;

use Attend\Database\Account;
use Attend\Database\TokenQuery;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;

require_once '../lib/bootstrap.php';
$config = bootstrap();
$logger = new Logger('logouts');
$logger->pushHandler(new StreamHandler('../logs/security.log'));

if (empty($_SESSION['account'])) {
    $logger->warning('Unexpected logout without session');
} else {
    /** @var Account $acct */
    $acct = $_SESSION['account'];
    $logger->info(sprintf('User "%s" logged out', $acct->getUsername()));

    // Delete "remember me" tokens when user explicitly logs out
    $tokens = TokenQuery::create()->findByAccountId($acct->getId());
    foreach ($tokens as $token) {
        try {
            $token->delete();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
    unset($_SESSION['account']);
}

setcookie("account_id", null, time() - 1);
setcookie("token", null, time() - 1);
session_destroy();
header('Location: /attend');

