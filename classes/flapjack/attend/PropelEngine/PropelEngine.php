<?php
namespace Attend\PropelEngine;


use Attend\Database\AccountQuery;
use Attend\Database\ScheduleQuery;
use Attend\Database\StudentQuery;
use Attend\IDatabaseEngine;

class PropelEngine implements IDatabaseEngine
{
    public function __construct()
    {
    }

    public function connect(array $config)
    {
        $host     = $config[ 'host' ];
        $dbname   = $config[ 'dbname' ];
        $user     = $config[ 'uname' ];
        $password = $config[ 'pword' ];

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

    public function getAccounts(): array
    {
        $query    = AccountQuery::create();
        $resource = $query->find();
        if (null === $resource) {
            return [];
        }

        return $resource->toArray();
    }

    public function getAccount($id): array
    {
        $query    = AccountQuery::create();
        $resource = $query->findOneById($id);
        if (null === $resource) {
            return [];
        }

        return $resource->toArray();
    }


    public function getClassroomById(int $id): array
    {
        $query    = new \Attend\Database\ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return [];
        }

        return $resource->toArray();
    }


    public function getClassrooms() : array
    {
        $query    = new \Attend\Database\ClassroomQuery();
        $resource = $query->find();

        return $resource->toArray();
    }

    public function postClassroom(array $body) : array
    {
        $resource = new \Attend\Database\Classroom();
        $resource->setLabel($body[ 'Label' ]);
        $resource->setOrdering($body[ 'Ordering' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function putClassroomById(int $id, array $body) : array
    {
        $query    = new \Attend\Database\ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return [];
        }

        $resource->setLabel($body[ 'Label' ]);
        $resource->setOrdering($body[ 'Ordering' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function deleteClassroomById(int $id) : int
    {
        $query    = new \Attend\Database\ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return 0;
        }
        $resource->delete();

        return $id;
    }

    public function getStudentById(int $id): ?array
    {
        $query    = new StudentQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        return $resource->toArray();
    }

    public function getStudents() : array
    {
        $query    = new StudentQuery();
        $resource = $query->find();

        return $resource->toArray();
    }


    public function postStudent(array $body) : array
    {
        $resource = new \Attend\Database\Student();
        $resource->setFamilyName($body[ 'FamilyName' ]);
        $resource->setFirstName($body[ 'FirstName' ]);
        $resource->setEnrolled($body[ 'Enrolled' ]);
        $resource->setClassroomId($body[ 'ClassroomId' ]);
        $resource->save();

        return $resource->toArray();
    }


    public function putStudentById(int $id, array $body): ?array
    {
        $query    = new StudentQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        $resource->setFamilyName($body['FamilyName']);
        $resource->setFirstName($body['FirstName']);
        $resource->setEnrolled($body['Enrolled']);
        $resource->setClassroomId($body['ClassroomId']);
        $resource->save();

        return $resource->toArray();
    }

    public function deleteStudentById(int $id): ?int
    {
        $query    = new StudentQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }
        $resource->delete();

        return $id;
    }

    public function getScheduleById(int $id): ?array
    {
        $query    = new ScheduleQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        return $resource->toArray();
    }

    public function getSchedules() : array
    {
        $query    = new ScheduleQuery();
        $resource = $query->find();

        return $resource->toArray();
    }

    public function postSchedule(array $body) : array
    {
        $resource = new \Attend\Database\Schedule();
        $resource->setStartDate($body[ 'StartDate' ]);
        $resource->setSchedule($body[ 'Schedule' ]);
        $resource->setStudentId($body[ 'StudentId' ]);
        $resource->save();

        return $resource->toArray();
    }

    public function putScheduleById(int $id, array $body): ?array
    {
        $query    = new ScheduleQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        $resource->setStartDate($body['StartDate']);
        $resource->setSchedule($body['Schedule']);
        $resource->setStudentId($body['StudentId']);
        $resource->save();

        return $resource->toArray();
    }


    public function deleteScheduleById(int $id): ?int
    {
        $query    = new ScheduleQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }
        $resource->delete();

        return $id;
    }
}