<?php


namespace Attend;


use Attend\Database\Account;
use Attend\Database\AccountQuery;
use Attend\PropelEngine\PropelEngine;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\App;


class ApiApp extends App
{
    /** @var null IDatabaseEngine */
    private $dbEngine;

    public function __construct($container = [])
    {
        $this->dbEngine = null;
        parent::__construct($container);
    }

    /**
     * @return null
     */
    public function getDbEngine()
    {
        if (!$this->dbEngine) {
            $c = $this->getContainer();
            $this->engine = $c->get('dbEngine');
        }
        return $this->dbEngine;
    }


    /**
     * @param null $dbEngine
     */
    public function setDbEngine(IDatabaseEngine $dbEngine)
    {
        $this->dbEngine = $dbEngine;
    }


    public function run($silent = false)
    {
        // Classrooms
        $engine = $this->getDbEngine();

        $this->get('/api/classrooms/{id}',
            function (Request $request, Response $response, array $args) {

                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $resource = $engine->getClassroomById($args['id']);
                if (null === $resource) {
                    return $response->withStatus(404, 'Not Found');
                }
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($resource));

                return $response;
            });

        $this->get('/api/classrooms',
            function (Request $request, Response $response, array $args) {

                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->getClassrooms();

                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->post('/api/classrooms',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->postClassroom($request->getParsedBody());
                if (null === $results) {
                    return $response->withStatus(404, 'Not Found');
                }

                $response = $response->withStatus(201, 'Created');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->put('/api/classrooms/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->putClassroomById($args['id'], $request->getParsedBody());
                if (null === $results) {
                    return $response->withStatus(404, 'Not Found');
                }
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->delete('/api/classrooms/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                if (!$engine->deleteClassroomById($args['id'])) {
                    $response = $response->withStatus(404, 'Not Found');

                    return $response;
                }
                $response = $response->withStatus(204, 'No Content');
                $response = $response->withHeader('Content-Type', 'application/json');

                return $response;
            });


        // Students
        $this->get('/api/students/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->getStudentById($args['id']);
                if (null === $results) {
                    return $response->withStatus(404, 'Not Found');
                }
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->get('/api/students',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->getStudents();
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->post('/api/students',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $id = $engine->postStudent($request->getParsedBody());
                $response = $response->withStatus(201, 'Created');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($id));

                return $response;
            });

        $this->put('/api/students/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                if (!$engine->putStudentById($args['id'], $request->getParsedBody())) {
                    return $response->withStatus(404, 'Not Found');
                }
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($args['id']));

                return $response;
            });

        $this->delete('/api/students/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                if (!$engine->deleteStudentById($args['id'])) {
                    $response = $response->withStatus(404, 'Not Found');

                    return $response;
                }
                $response = $response->withStatus(204, 'No Content');
                $response = $response->withHeader('Content-Type', 'application/json');

                return $response;
            });


        // Schedules
        $this->get('/api/schedules/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->getScheduleById($args['id']);
                if (null === $results) {
                    return $response->withStatus(404, 'Not Found');
                }
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->get('/api/schedules',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $results = $engine->getSchedules($request, $response, $args);
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-type', 'application/json');
                $response->getBody()->write(json_encode($results));

                return $response;
            });

        $this->post('/api/schedules',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                $id = $engine->postSchedule($request->getParsedBody());
                $response = $response->withStatus(201, 'Created');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($id));

                return $response;
            });

        $this->put('/api/schedules/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                if (!$engine->putScheduleById($args['id'], $request->getParsedBody())) {
                    return $response->withStatus(404, 'Not Found');
                }
                $response = $response->withStatus(200, 'OK');
                $response = $response->withHeader('Content-Type', 'application/json');
                $response->getBody()->write(json_encode($args['id']));

                return $response;
            });

        $this->delete('/api/schedules/{id}',
            function (Request $request, Response $response, array $args) {
                /** @var PropelEngine $engine */
                $engine = $this->get('dbEngine');
                return $engine->deleteScheduleById($request, $response, $args);
            });

        $this->post('/api/accounts', function (Request $request, Response $response, array $args = []) {
            // Insert a new record into the Accounts table
            $body = $request->getParsedBody();
            $acct = new Account();
            $acct->setUsername($body['username']);
            $acct->setEmail($body['email']);
            $acct->setPwhash(password_hash($body['password'], PASSWORD_BCRYPT));
            $acct->setRole($body['role']);
            $acct->save();

            $response = $response->withStatus(201, 'Created');
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($acct->getId()));

            return $response;
        });

        $this->put('/api/accounts/{id}', function (Request $request, Response $response, array $args = []) {
            // Update an existing record in the Accounts table
            $body = $request->getParsedBody();
            $acct = AccountQuery::create()->findPk($body['id']);

            $acct->setUsername($body['username']);
            $acct->setEmail($body['email']);
            if (!empty($body['password'])) {
                $acct->setPwhash(password_hash($body['password'], PASSWORD_BCRYPT));
            }
            $acct->setRole($body['role']);
            $acct->save();

            $response = $response->withStatus(200, 'OK');
            $response = $response->withHeader('Content-Type', 'application/json');
            $response->getBody()->write(json_encode($acct->getId()));

            return $response;
        });

        return parent::run($silent); // TODO: Change the autogenerated stub
    }
}
