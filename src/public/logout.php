<?php

namespace Attend;

use Attend\Database\TokenQuery;

require_once '../lib/bootstrap.php';
$config = bootstrap();

if (!empty($_SESSION['account'])) {
    $acct = $_SESSION['account'];

    // Delete "remember me" tokens when user explicitly logs out
    $tokens = TokenQuery::create()->findByAccountId($acct->getId());
    foreach ($tokens as $token) {
        try {
            $token->delete();
        } catch (\Exception $e) {
            die($e->getMessage());
        }
    }
}

unset($_SESSION['account']);
setcookie("account_id", null, time() - 1);
setcookie("token", null, time() - 1);
session_destroy();
header('Location: /attend');

