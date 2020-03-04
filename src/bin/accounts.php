<?php

namespace Attend;

use Attend\Database\Account;
use Propel\Runtime\Exception\PropelException;

function usage()
{
    echo "Manage Attend user and admin accounts\n";
    echo "\n";
    echo "Usage: php accounts.php [command] {args}\n";
    echo "\n";
    echo "where [command] is required and one of:\n";
    echo "   add: Add a new account\n";
    echo "   del: Remove an account\n";
    echo "\n";
    echo "and {args} are arguments, specific to the command\n";
    echo "\n";
    echo "add [username] [password] [email] [role]\n";
    echo "\n";
    echo "del [username]\n";
    echo "\n";


    return;
}


function addUser($username, $password, $email, $role)
{
    if (!$username) {
        echo 'Missing required parameter "username"' . "\n";
        usage();;
        die();
    }
    if (!$password) {
        echo 'Missing required parameter "password"' . "\n";
        usage();
        die();
    }

    $acct = new Account();
    $acct->setUsername($username);
    $acct->setEmail($email);
    $acct->setPwhash(password_hash($password, PASSWORD_BCRYPT));
    $acct->setRole($role);

    try {
        $acct->save();
        echo 'ok' . "\n";
    } catch (PropelException $e) {
        die("Error saving user: " . $e->getMessage() . "\n");
    }

    echo "Added user ID " . $acct->getId() . "\n";
}


function delUser($username)
{
    echo "TODO: Delete user $username\n";
}


require_once('../lib/bootstrap.php');
bootstrap();

// $argv[0] is 'accounts.php'
$command = $argv[1];
switch ($command) {

    case 'add':
        addUser($argv[2], $argv[3], $argv[4], $argv[5]);
        break;

    case 'del':
        delUser($argv[2]);
        break;

    case '--help':
        usage();
        exit();
        break;

    default:
        echo('Unknown command "' . $command . '"\n');
        usage();
        exit();
}


//$opts = getopt('u:p:e:r:', ['username:', 'password:', 'email:', 'role:']);
//var_dump($opts);
