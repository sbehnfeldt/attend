<?php
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class PropelEngine
{

    public function __construct()
    {
    }

    public function connect($host, $dbname, $user, $password)
    {
        $serviceContainer = \Propel\Runtime\Propel::getServiceContainer();
        $serviceContainer->checkVersion('2.0.0-dev');
        $serviceContainer->setAdapterClass('attend', 'mysql');
        $manager = new \Propel\Runtime\Connection\ConnectionManagerSingle();
        $manager->setConfiguration(array(
            'classname'   => 'Propel\\Runtime\\Connection\\ConnectionWrapper',
            'dsn'         => "mysql:host=$host;dbname=$dbname",
            'user'        => $user,
            'password'    => $password,
            'attributes'  =>
                array(
                    'ATTR_EMULATE_PREPARES' => false,
                    'ATTR_TIMEOUT'          => 30,
                ),
            'model_paths' =>
                array(
                    0 => 'src',
                    1 => 'vendor',
                ),
        ));
        $manager->setName('attend');
        $serviceContainer->setConnectionManager('attend', $manager);
        $serviceContainer->setDefaultDatasource('attend');
    }


    public function getClassroomById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query   = new \Attend\Database\ClassroomQuery();
        $results = $query->findPk($args[ 'id' ]);
        if (null === $results) {
            $response = $response->withStatus(404, 'Not Found');
            return $response;
        }

        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($results->toJSON());

        return $response;
    }


    public function getClassrooms(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query    = new \Attend\Database\ClassroomQuery();
        $results  = $query->find();
        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write($results->toJSON());

        return $response;
    }

    public function postClassroom(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body     = $request->getParsedBody();
        $resource = new \Attend\Database\Classroom();
        $resource->setLabel($body[ 'Label' ]);
        $resource->setOrdering($body[ 'Ordering' ]);
        $resource->save();

        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($resource->getId()));

        return $response;
    }

    public function putClassroomById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query   = new \Attend\Database\ClassroomQuery();
        $results = $query->findPk($args[ 'id' ]);
        if (null === $results) {
            $response = $response->withStatus(404, 'Not Found');
            return $response;
        }

        $body = $request->getParsedBody();
        $results->setLabel($body[ 'Label' ]);
        $results->setOrdering($body[ 'Ordering' ]);
        $results->save();

        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write($results->toJSON());

        return $response;
    }

    public function deleteClassroomById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query   = new \Attend\Database\ClassroomQuery();
        $results = $query->findPk($args[ 'id' ]);
        if (null === $results) {
            $response = $response->withStatus(404, 'Not Found');
            return $response;
        }
        $results->delete();

        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }

    public function getStudentById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query   = new \Attend\Database\StudentQuery();
        $results = $query->findPk($args[ 'id' ]);
        if (null === $results) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }

        $response = $response->withStatus(200, 'OK');
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write($results->toJSON());

        return $response;
    }

    public function getStudents(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query    = new \Attend\Database\StudentQuery();
        $results  = $query->find();
        $response = $response->withHeader('Content-type', 'application/json');
        $response->getBody()->write($results->toJSON());

        return $response;
    }


    public function postStudent(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $body     = $request->getParsedBody();
        $resource = new \Attend\Database\Student();
        $resource->setFamilyName($body[ 'FamilyName' ]);
        $resource->setFirstName($body[ 'FirstName' ]);
        $resource->setEnrolled($body[ 'Enrolled' ]);
        $temp = json_decode($body[ 'ClassroomId' ]);
        $resource->setClassroomId($temp->data);
        $resource->save();

        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($resource->getId()));

        return $response;
    }


    public function putStudentById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query    = new \Attend\Database\StudentQuery();
        $resource = $query->findPk($args[ 'id' ]);
        if (null === $resource) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }

        $body = $request->getParsedBody();
        $resource->setFamilyName($body[ 'FamilyName' ]);
        $resource->setFirstName($body[ 'FirstName' ]);
        $resource->setEnrolled($body[ 'Enrolled' ]);
        $temp = json_decode($body[ 'ClassroomId' ]);
        $resource->setClassroomId($temp->data);
        $resource->save();

        $response = $response->withStatus(201, 'Created');
        $response = $response->withHeader('Content-Type', 'application/json');
        $response->getBody()->write(json_encode($resource->getId()));

        return $response;
    }

    public function deleteStudentById(ServerRequestInterface $request, ResponseInterface $response, array $args)
    {
        $query   = new \Attend\Database\StudentQuery();
        $results = $query->findPk($args[ 'id' ]);
        if (null === $results) {
            $response = $response->withStatus(404, 'Not Found');

            return $response;
        }
        $results->delete();

        $response = $response->withStatus(204, 'No Content');
        $response = $response->withHeader('Content-Type', 'application/json');

        return $response;
    }
}
