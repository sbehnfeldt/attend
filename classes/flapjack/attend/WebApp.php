<?php
namespace flapjack\attend;


use flapjack\attend\database\TokenAuth;
use flapjack\attend\database\TokenAuthQuery;
use flapjack\attend\database\Account;
use flapjack\attend\database\AccountQuery;
use flapjack\attend\database\LoginAttempt;
use flapjack\attend\database\ClassroomQuery;
use flapjack\attend\database\LoginAttemptQuery;
use flapjack\attend\database\PermissionQuery;

use DateInterval;
use DateTime;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Propel\Runtime\Exception\PropelException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;


/**
 * @param  DateTime  $d
 *
 * @return DateTime
 * @throws Exception
 *
 * Find the DateTime object representing the Monday closest to the input date
 */
function getMonday(DateTime $d)
{
    $dw = $d->format('N');
    switch ($dw) {
        case 1:
            break;
        case 2:  // Tuesday
            $d = $d->sub(new DateInterval('P1D'));
            break;
        case 3:  // Wednesday
            $d = $d->sub(new DateInterval('P2D'));
            break;
        case 4:  // Thursday
            $d = $d->sub(new DateInterval('P3D'));
            break;

        case 5:  // Friday
            $d = $d->add(new DateInterval('P3D'));
            break;
        case 6:  // Saturday
            $d = $d->add(new DateInterval('P2D'));
            break;
        case 7:  // Sunday
            $d = $d->add(new DateInterval('P1D'));
            break;

        default:
            throw new Exception(sprintf('Unknown day of the week "%d"', $dw));
            break;
    }

    return $d;
}


class WebApp extends App
{
    /** @var Environment|null */
    private $twig;

    /** @var Logger */
    private $logger;


    public function __construct($container = [])
    {
        parent::__construct($container);
        $this->twig
            = $this->logger
            = null;
    }

    /**
     * @return Environment
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getTwig(): Environment
    {
        if ( ! $this->twig) {
            $f = new TwigFunction('getDate', function (DateTime $weekOf, int $d) {
                $w = new DateTime($weekOf->format('Y/m/d'));
                $w->add(new DateInterval(sprintf('P%dD', $d)));

                return $w->format('M j');
            });

            if ($this->getContainer()->has('twig')) {
                $this->twig = $this->getContainer()->get('twig');
                $this->twig->addFunction($f);
            }

            if ( ! $this->twig) {
                $loader     = new FilesystemLoader('../templates');
                $this->twig = new Environment($loader, array(
                    'cache' => false,
                    'debug' => true
                ));
                $this->twig->addFunction($f);
                $this->twig->addExtension(new \Twig\Extension\DebugExtension());
            }
        }

        return $this->twig;
    }

    /**
     * @param  Environment  $twig
     */
    public function setTwig(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return Logger
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function getLogger(): Logger
    {
        if ( ! $this->logger) {
            if ($this->getContainer()->has('logger')) {
                $this->logger = $this->getContainer()->get('logger');
            }

            if ( ! $this->logger) {
                $this->logger = new Logger('attend');
                $this->logger->pushHandler(new StreamHandler('../logs/attend.log'));
            }
        }

        return $this->logger;
    }


    /**
     * @param  Logger  $logger
     */
    public function setLogger(Logger $logger): void
    {
        $this->logger = $logger;
    }


    /**
     * @param  string  $username
     * @param  string  $password
     *
     * @return true|void
     * @throws ContainerExceptionInterface
     * @throws MissingLoginCredentialsException
     * @throws NotFoundExceptionInterface
     * @throws PropelException
     * @throws UnauthorizedLoginAttemptException
     *
     * Process username/password login attempt
     */
    public function authenticate(string $username, string $password)
    {
        try {
            $this->validateCredentialsNonempty($username, $password);
            $acct = $this->lookup($username);
            $this->verify($password, $acct);
        } catch (UnauthorizedLoginAttemptException|MissingLoginCredentialsException $e) {
            $this->getLogger()->warning($e->getMessage());
            throw $e;
        }
        $this->acceptUser($acct);

        return true;
    }


    /**
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     *
     * Log user out
     */
    public function logout(): void
    {
        if (empty($_SESSION['account'])) {
            $this->getLogger()->warning('Unexpected logout without session');
        } else {
            /** @var Account $acct */
            $acct = $_SESSION['account'];
            $this->getLogger()->info(sprintf('User "%s" logged out', $acct->getUsername()));
            $login = LoginAttemptQuery::create()
                                      ->filterByUsername($acct->getUsername())
                                      ->orderByAttemptedAt('desc')
                                      ->findOne();
            $login->setLoggedOutAt(time());
            $login->save();

            // Delete "remember me" tokens when user explicitly logs out
            $tokens = TokenAuthQuery::create()->findByAccountId($acct->getId());
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
    }


    /**
     * @param  string  $username
     * @param  string  $password
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws MissingLoginCredentialsException
     * @throws NotFoundExceptionInterface
     * @throws PropelException
     *
     * Validate that the provided username and password are not empty
     */
    private function validateCredentialsNonempty(string $username, string $password): void
    {
        if (empty($username) || empty($password)) {
            $note    = 'Invalid login attempt: missing username or password';
            $attempt = new LoginAttempt();
            $attempt->setUsername('');
            $attempt->setAttemptedAt(time());
            $attempt->setPass(0);
            $attempt->setNote($note);
            $attempt->save();
            throw new MissingLoginCredentialsException($note);
        }
    }

    /**
     * @param  string  $password
     * @param  Account  $acct
     *
     * @return void
     * @throws PropelException
     * @throws UnauthorizedLoginAttemptException
     *
     * Verify user credentials and log results
     */
    private function verify(string $password, Account $acct)
    {
        if ( ! password_verify($password, $acct->getPwhash())) {
            // Wrong password
            $note    = sprintf('Login denied: incorrect password for user "%s"', $acct->getUsername());
            $attempt = new LoginAttempt();
            $attempt->setUsername($acct->getUsername());
            $attempt->setAttemptedAt(time());
            $attempt->setPass(0);
            $attempt->setNote($note);
            $attempt->save();
            throw new UnauthorizedLoginAttemptException($note);
        }
    }


    /**
     * @param  string  $username
     *
     * @return Account
     * @throws UnauthorizedLoginAttemptException
     * @throws PropelException
     *
     * Find the account by the specified username and log results
     */
    private function lookup(string $username): Account
    {
        if ( ! ($acct = AccountQuery::create()->findOneByUsername($username))) {
            // User not found
            $note    = sprintf('Login denied: no account for user "%s"', $username);
            $attempt = new LoginAttempt();
            $attempt->setUsername($username);
            $attempt->setAttemptedAt(time());
            $attempt->setPass(0);
            $attempt->setNote($note);
            $attempt->save();
            throw new UnauthorizedLoginAttemptException($note);
        }

        return $acct;
    }


    /**
     * @param  Account  $acct
     *
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws PropelException
     *
     * Accept
     */
    private function acceptUser(Account $acct)
    {
        $_SESSION['account'] = $acct;
        $this->getLogger()->info(sprintf('User "%s" successfully logged in', $acct->getUsername()));
        $attempt = new LoginAttempt();
        $attempt->setUsername($acct->getUsername());
        $temp = time();
        $attempt->setAttemptedAt(time());
        $attempt->setPass(1);
        $attempt->setNote('Authenticated');
        $attempt->save();

        if (empty($_POST['remember'])) {
            // Clear any current auth cookie
            setcookie("account_id", null, time() - 1);
            setcookie("token", null, time() - 1);
        } else {
            // Mark any existing "Remember Me" tokens as expired
            $tokens = TokenAuthQuery::create()->findByAccountId($acct->getId());
            foreach ($tokens as $token) {
                try {
                    $token->delete();
                } catch (Exception $e) {
                    die($e->getMessage());
                }
            }

            //
//            $expiration = time() + (30 * 24 * 60 * 60);  // for 1 month
            $expiration = time() + (5 * 60 * 60);  // for 5 minutes
            setcookie("account_id", $acct->getId(), $expiration);
            $random = getToken(32);
            setcookie("token", $random, $expiration);

            $token = new TokenAuth();
            $token->setAccountId($acct->getId());
            $token->setCookieHash(password_hash($random, PASSWORD_DEFAULT));
            $token->setExpires(date("Y-m-d H:i:s", $expiration));
            try {
                $token->save();
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Routing for Web App Pages
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    public function run($silent = false)
    {
        $web = $this;

        // Middleware to ensure user is logged in
        $isAuthenticated = function (Request $request, Response $response, $next) use ($web) {
            if (empty($_SESSION['account'])) {
                // If this value is empty, then the user is not logged in.

                // Check for "remember me" cookies; validate if found
                // ref: https://phppot.com/php/secure-remember-me-for-login-using-php-session-and-cookies/
                if ( ! empty($_COOKIE["account_id"]) && ! empty($_COOKIE["token"])) {
                    $tokens = TokenAuthQuery::create()->filterByAccountId($_COOKIE['account_id']);
                    /** @var TokenAuth $token */
                    foreach ($tokens as $token) {
                        $tk      = $_COOKIE['token'];
                        $hash    = $token->getCookieHash();
                        $expires = $token->getExpires();
                        $now     = new DateTime(time());
                        $expired = ($expires < $now);

                        // If password is verified
                        // AND token expiry is later than current time
                        if (password_verify($tk, $hash) && ! $expired) {
                            $_SESSION['account'] = AccountQuery::create()->findPk($_COOKIE['account_id']);
                            break;
                        }
                    }
                }
            }

            if (empty($_SESSION['account'])) {
                // Still not authenticated, neither by session nor "remember me" cookies
                return $response->withHeader('Location', '/login');
            }

            // User is authenticated
            $response = $next($request, $response);

            return $response;
        };

        // Middleware to restrict admin-only pages to admins only
        $adminOnly = function (Request $request, Response $response, $next) {
            $loader = new FilesystemLoader('../templates');
            $twig   = new Environment($loader, array(
                'cache' => false
            ));
            if (empty($_SESSION['account'])) {
                $response->getBody()->write(
                    $twig->render('login.html.twig', [
                        'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
                    ])
                );

                return $response;
            }

            if ('admin' !== $_SESSION['account']->getRole()) {
                $response = $response->withStatus(403);
                $response->getBody()->write(
                    $twig->render('403.html.twig', [
                        'account' => $_SESSION['account']
                    ])
                );

                return $response;
            }
            $response = $next($request, $response);

            return $response;
        };


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        /// Handle login form submission
        $this->post('/login', function (Request $request, Response $response, array $args) use ($web) {
            try {
                $web->authenticate($_POST['username'], $_POST['password']);
            } catch (MissingLoginCredentialsException $e) {
                header('Content-Type: application/json');
                die (
                json_encode([
                    'invalid'  => true,
                    'username' => ! empty($_POST['username']),
                    'password' => ! empty($_POST['password'])
                ])
                );
            } catch (UnauthorizedLoginAttemptException $e) {
                header('Content-Type: application/json');
                die (
                json_encode([
                    'unauthorized' => true
                ])
                );
            }

            header('Content-Type: application/json');
            exit(
            json_encode([
                'Location' => '/'
            ])
            );
        });


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        /// Handle Logout submission
        $this->get('/logout', function (Request $request, Response $response, array $args) use ($web) {
            $web->logout();

            return $response
                ->withStatus(301)
                ->withHeader('Location', '/');
        });


        //////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /// Routes to Pages
        //////////////////////////////////////////////////////////////////////////////////////////////////////////////


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        /// Home page
        $this->get('/', function (Request $request, Response $response, array $args) use ($web) {
            /** @var Account $account */
            $account = $_SESSION['account'];
            if ( ! $account->hasPermission('PAGE_ATTENDANCE')) {
                $response->getBody()->write($web->getTwig()->render('403.html.twig', []));

                return $response->withStatus(403);
            }
            $response->getBody()->write(
                $web->getTwig()->render('index.html.twig', [
                    'account' => $account,
                    'perms'   => $account->getPermissionsList()
                ])
            );

            return $response;
        })->add($isAuthenticated);


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        ///Login page
        $this->get('/login', function (Request $request, Response $response, array $args) use ($web) {
            $response->getBody()->write(
                $web->getTwig()->render('login.html.twig', [
                    'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
                ])
            );

            return $response;
        });


        $this->get('/attendance', function (Request $request, Response $response, array $args) use ($web) {
            /** @var Account $account */
            $account = $_SESSION['account'];
            if ( ! $account->hasPermission('PAGE_ATTENDANCE')) {
                $response->getBody()->write(
                    $web->getTwig()->render('403.html.twig', [
                        'account' => $account,
                        'perms'   => $account->getPermissionsList()
                    ])
                );

                return $response->withStatus(403);
            }

            // Get the text version of the date, suitable for column header
            $weekOf = new DateTime('now');
            $weekOf = getMonday($weekOf);

            $response->getBody()->write(
                $web->getTwig()->render('attendance.html.twig', [
                    'account'    => $account,
                    'perms'      => $account->getPermissionsList(),
                    'classrooms' => ClassroomQuery::create()->find(),
                    'weekOf'     => $weekOf
                ])
            );

            return $response;
        })->add($isAuthenticated);


        $this->get('/enrollment', function (Request $request, Response $response, array $args) use ($web) {
            /** @var Account $account */
            $account = $_SESSION['account'];
            if ( ! $account->hasPermission('PAGE_ENROLLMENT')) {
                $response->getBody()->write(
                    $web->getTwig()->render('403.html.twig', [
                        'account' => $account,
                        'perms'   => $account->getPermissionsList()
                    ])
                );

                return $response->withStatus(403);
            }

            $response->getBody()->write(
                $web->getTwig()->render('enrollment.html.twig', [
                    'account' => $account,
                    'perms'   => $account->getPermissionsList()
                ])
            );

            return $response;
        })->add($isAuthenticated);


        $this->get('/classrooms', function (Request $request, Response $response, array $args) use ($web) {
            /** @var Account $account */
            $account = $_SESSION['account'];
            if ( ! $account->hasPermission('PAGE_CLASSROOMS')) {
                $response->getBody()->write(
                    $web->getTwig()->render('403.html.twig', [
                        'account' => $account,
                        'perms'   => $account->getPermissionsList()
                    ])
                );

                return $response->withStatus(403);
            }

            $response->getBody()->write(
                $web->getTwig()->render('classrooms.html.twig', [
                    'account' => $account,
                    'perms'   => $account->getPermissionsList(),
                ])
            );

            return $response;
        })->add($isAuthenticated);


        $this->get('/admin', function (Request $request, Response $response, array $args) use ($web) {
            /** @var Account $account */
            $account = $_SESSION['account'];
            if ( ! $account->hasPermission('PAGE_ADMIN')) {
                $response->getBody()->write(
                    $web->getTwig()->render('403.html.twig', [
                        'account' => $account,
                        'perms'   => $account->getPermissionsList()
                    ])
                );

                return $response->withStatus(403);
            }

            $accounts    = AccountQuery::create()->find();
            $logins      = LoginAttemptQuery::create()->find();
            $permissions = PermissionQuery::create()->find();

            $response->getBody()->write(
                $web->getTwig()->render('admin.html.twig', [
                    'account'     => $account,
                    'perms'       => $account->getPermissionsList(),
                    'accounts'    => $accounts,
                    'logins'      => $logins,
                    'permissions' => $permissions
                ])
            );

            return $response;
        })->add($isAuthenticated)->add($adminOnly);


        $this->get('/profile', function (Request $request, Response $response) use ($web) {
            /** @var Account $account */
            $account = $_SESSION['account'];
            if ( ! $account->hasPermission('PAGE_PROFILE')) {
                $response->getBody()->write(
                    $web->getTwig()->render('403.html.twig', [
                        'account' => $account,
                        'perms'   => $account->getPermissionsList()
                    ])
                );

                return $response->withStatus(403);
            }

            $response->getBody()->write(
                $web->getTwig()->render('profile.html.twig', [
                    'account' => $account,
                    'perms'   => $account->getPermissionsList()
                ])
            );

            return $response;
        })->add($isAuthenticated);

        parent::run($silent);
    }
}
