<?php


namespace flapjack\attend;


use flapjack\attend\MissingLoginCredentialsException;
use flapjack\attend\UnauthorizedLoginAttemptException;
use flapjack\attend\database\Account;
use flapjack\attend\database\AccountQuery;
use flapjack\attend\database\LoginAttempt;
use flapjack\attend\database\ClassroomQuery;
use flapjack\attend\database\LoginAttemptQuery;
use flapjack\attend\database\PermissionQuery;

//use Attend\Database\Token;
//use Attend\Database\TokenQuery;
use DateInterval;
use DateTime;
use Exception;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

// Find the DateTime object representing the Monday closest to the input date
/**
 * @param  DateTime  $d
 *
 * @return DateTime
 * @throws Exception
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
     * @return Environment|null
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
     * @param  Environment|null  $twig
     */
    public function setTwig(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @return Logger
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


    public function login($username, $password)
    {
        if (empty($username) || empty($password)) {
            $note    = 'Invalid login attempt: missing username or password';
            $attempt = new LoginAttempt();
            $attempt->setAttemptedAt(time());
            $attempt->setPass(0);
            $attempt->setNote($note);
            $attempt->save();
            $this->getLogger()->info($note);
            throw new MissingLoginCredentialsException($note);
        }

        // Look up user in Accounts table
//        $acct = AccountQuery::create()->findOneByUsername($username);
//        $q = new AccountQuery();
        $q    = AccountQuery::create();
        $acct = $q->findOneByUsername($username);
        if ( ! $acct) {
            // User not found
            $note    = sprintf('Login denied: no account for user "%s"', $username);
            $attempt = new LoginAttempt();
            $attempt->setUsername($username);
            $attempt->setAttemptedAt(time());
            $attempt->setPass(0);
            $attempt->setNote($note);
            $attempt->save();
            $this->getLogger()->info($note);
            throw new UnauthorizedLoginAttemptException($note);
        }
        if ( ! password_verify($password, $acct->getPwhash())) {
            // Wrong password
            $note    = sprintf('Login denied: incorrect password for user "%s"', $username);
            $attempt = new LoginAttempt();
            $attempt->setUsername($username);
            $attempt->setAttemptedAt(time());
            $attempt->setPass(0);
            $attempt->setNote($note);
            $attempt->save();
            $this->getLogger()->info($note);
            throw new UnauthorizedLoginAttemptException($note);
        }
        // User authenticated
        $this->getLogger()->info(sprintf('User "%s" successfully logged in', $username));
        $attempt = new LoginAttempt();
        $attempt->setUsername($username);
        $attempt->setAttemptedAt(time());
        $attempt->setPass(1);
        $attempt->setNote('Authenticated');
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
                } catch (Exception $e) {
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
            } catch (Exception $e) {
                die($e->getMessage());
            }
        }

        return true;
    }


    public function logout()
    {
        if (empty($_SESSION['account'])) {
            $this->getLogger()->warning('Unexpected logout without session');
        } else {
            /** @var Account $acct */
            $acct = $_SESSION['account'];
            $this->getLogger()->info(sprintf('User "%s" logged out', $acct->getUsername()));

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
                // Check for "remember me" cookies, validate if found
                // ref: https://phppot.com/php/secure-remember-me-for-login-using-php-session-and-cookies/
                if ( ! empty($_COOKIE["account_id"]) && ! empty($_COOKIE["token"])) {
                    $tokens = TokenQuery::create()->filterByAccountId($_COOKIE['account_id']);
                    /** @var Token $token */
                    foreach ($tokens as $token) {
                        if (password_verify($_COOKIE["token"], $token->getCookieHash()) && $token->getExpires() >= date(
                                "Y-m-d H:i:s",
                                time()
                            )) {
                            $_SESSION['account'] = AccountQuery::create()->findPk($_COOKIE['account_id']);
                            break;
                        }
                    }
                }
            }

            if (empty($_SESSION['account'])) {
                // Not authenticated: neither by session nor "remember me" cookies
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
                $web->login($_POST['username'], $_POST['password']);
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
            die(
            json_encode([
                'Location' => $_POST['route']
            ])
            );
        });


        ////////////////////////////////////////////////////////////////////////////////////////////////////
        /// Handle Logout submission
        $this->get('/logout', function (Request $request, Response $response, array $args) use ($web) {
            $web->logout();

            return $response
                ->withStatus(301)
                ->withHeader('Location', '/attend');
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
