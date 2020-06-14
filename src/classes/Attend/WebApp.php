<?php


namespace Attend;


use Attend\Database\AccountQuery;
use Attend\Database\ClassroomQuery;
use Attend\Database\LoginAttemptQuery;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;
use Twig\Environment;
use Twig\Loader\FilesystemLoader;
use Twig\TwigFunction;

class WebApp extends App
{

    public function routes()
    {

////////////////////////////////////////////////////////////////////////////////////////////////////
// Routing for Web App Pages
////////////////////////////////////////////////////////////////////////////////////////////////////
        $app->get('/', function (Request $request, Response $response, array $args) {
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));
            $response->getBody()->write($twig->render('index.html.twig', [
                'account' => $_SESSION['account'],
            ]));
            return $response;
        })->add($authenticate);


        $app->get('/attendance', function (Request $request, Response $response, array $args) {
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));

            // Get the text version of the date, suitable for column header
            $twig->addFunction(new TwigFunction('getDate', function (\DateTime $weekOf, int $d) {
                $w = new \DateTime($weekOf->format('Y/m/d'));
                $w->add(new \DateInterval(sprintf('P%dD', $d)));
                return $w->format('M j');
            }));

            $weekOf = new \DateTime('now');
            $weekOf = getMonday($weekOf);
            $response->getBody()->write($twig->render('attendance.html.twig', [
                'account' => $_SESSION['account'],
                'classrooms' => ClassroomQuery::create()->find(),
                'weekOf' => $weekOf
            ]));
            return $response;
        })->add($authenticate);


        $app->get('/enrollment', function (Request $request, Response $response, array $args) {
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));

            $response->getBody()->write($twig->render('enrollment.html.twig', [
                'account' => $_SESSION['account'],
            ]));
            return $response;
        })->add($authenticate);


        $app->get('/classrooms', function (Request $request, Response $response, array $args) {
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));
            $response->getBody()->write($twig->render('classrooms.html.twig', [
                'account' => $_SESSION['account'],
            ]));
            return $response;
        })->add($authenticate);

        $app->get('/admin', function (Request $request, Response $response, array $args) {
            $accounts = AccountQuery::create()->find();
            $logins = LoginAttemptQuery::create()->find();
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));
            $response->getBody()->write($twig->render('admin.html.twig', [
                'account' => $_SESSION['account'],
                'accounts' => $accounts,
                'logins' => $logins
            ]));
            return $response;
        })->add($authenticate)->add($adminOnly);

        $app->get('/profile', function (Request $request, Response $response) {
            $loader = new FilesystemLoader('../templates');
            $twig = new Environment($loader, array(
                'cache' => false
            ));

            $response->getBody()->write($twig->render('profile.html.twig', [
                'account' => $_SESSION['account']
            ]));

            return $response;
        })->add($authenticate);


    }

}