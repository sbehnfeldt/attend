<?php


namespace Attend;


use Attend\Database\AccountQuery;
use Attend\Database\ClassroomQuery;
use Attend\Database\LoginAttemptQuery;
use Attend\Database\Token;
use Attend\Database\TokenQuery;
use DateInterval;
use DateTime;
use Exception;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;


// Find the DateTime object representing the Monday closest to the input date
/**
 * @param DateTime $d
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
    public function __construct($container = [])
    {
        parent::__construct($container);
    }


    ////////////////////////////////////////////////////////////////////////////////////////////////////
    // Routing for Web App Pages
    ////////////////////////////////////////////////////////////////////////////////////////////////////
    public function routes()
    {

        // Middleware to ensure user is logged in
        $authenticate = function (Request $request, Response $response, $next) {
            if (empty($_SESSION['account'])) {
                // Check for "remember me" cookies, validate if found
                if (!empty($_COOKIE["account_id"]) && !empty($_COOKIE["token"])) {
                    $tokens = TokenQuery::create()->filterByAccountId($_COOKIE['account_id']);
                    /** @var Token $token */
                    foreach ($tokens as $token) {
                        if (password_verify($_COOKIE["token"], $token->getCookieHash()) && $token->getExpires() >= date("Y-m-d H:i:s", time())) {
                            $_SESSION['account'] = AccountQuery::create()->findPk($_COOKIE['account_id']);
                            break;
                        }
                    }
                }
            }

            if (empty($_SESSION['account'])) {
                // Not authenticated: neither by session nor "remember me" cookies
                $loader = new FilesystemLoader('../templates');
                $twig = new Environment($loader, array(
                    'cache' => false
                ));
                $response->getBody()->write($twig->render('login.html.twig', [
                    'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
                ]));
                return $response;
            }

            // User is authenticated
            $response = $next($request, $response);
            return $response;
        };

        // Middleware to restrict admin-only pages to admins only
        $adminOnly = function (Request $request, Response $response, $next) {
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));
            if (empty($_SESSION['account'])) {
                $response->getBody()->write($twig->render('login.html.twig', [
                    'route' => $_SERVER['CONTEXT_PREFIX'] . $request->getAttribute('route')->getPattern()
                ]));
                return $response;
            }

            if ('admin' !== $_SESSION['account']->getRole()) {
                $response = $response->withStatus(403);
                $response->getBody()->write($twig->render('403.html.twig', [
                    'account' => $_SESSION['account']
                ]));
                return $response;
            }
            $response = $next($request, $response);
            return $response;
        };


        $this->get('/', function (Request $request, Response $response, array $args) {
            /** @var callable $render */
            $render = $this->get('render');
            $response->getBody()->write($render('index.html.twig', [
                'account' => $_SESSION['account']
            ]));
            return $response;
        })->add($authenticate);


        $this->get('/attendance', function (Request $request, Response $response, array $args) {
            // Get the text version of the date, suitable for column header
            $weekOf = new DateTime('now');
            $weekOf = getMonday($weekOf);

            /** @var callable $render */
            $render = $this->get('render');
            $response->getBody()->write($render('attendance.html.twig', [
                'account' => $_SESSION['account'],
                'classrooms' => ClassroomQuery::create()->find(),
                'weekOf' => $weekOf
            ]));
            return $response;
        })->add($authenticate);


        $this->get('/enrollment', function (Request $request, Response $response, array $args) {
            /** @var callable $render */
            $render = $this->get('render');
            $response->getBody()->write($render('enrollment.html.twig', [
                'account' => $_SESSION['account'],
            ]));
            return $response;
        })->add($authenticate);


        $this->get('/classrooms', function (Request $request, Response $response, array $args) {
            /** @var callable $render */
            $render = $this->get('render');
            $response->getBody()->write($render('classrooms.html.twig', [
                'account' => $_SESSION['account'],
            ]));
            return $response;
        })->add($authenticate);


        $this->get('/admin', function (Request $request, Response $response, array $args) {
            $accounts = AccountQuery::create()->find();
            $logins = LoginAttemptQuery::create()->find();

            /** @var callable $render */
            $render = $this->get('render');
            $response->getBody()->write($render('admin.html.twig', [
                'account' => $_SESSION['account'],
                'accounts' => $accounts,
                'logins' => $logins
            ]));
            return $response;
        })->add($authenticate)->add($adminOnly);


        $this->get('/profile', function (Request $request, Response $response) {
            /** @var callable $render */
            $render = $this->get('render');
            $response->getBody()->write($render('profile.html.twig', [
                'account' => $_SESSION['account']
            ]));

            return $response;
        })->add($authenticate);


        return $this;
    }
}
