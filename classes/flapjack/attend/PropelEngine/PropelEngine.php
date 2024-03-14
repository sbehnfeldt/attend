<?php
namespace flapjack\attend\PropelEngine;


use flapjack\attend\database\AccountQuery;
use flapjack\attend\database\Classroom;
use flapjack\attend\database\ClassroomQuery;
use flapjack\attend\database\Map\ClassroomTableMap;
use flapjack\attend\database\Schedule;
use flapjack\attend\database\ScheduleQuery;
use flapjack\attend\database\Student;
use flapjack\attend\database\StudentQuery;
use flapjack\attend\IDatabaseEngine;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\Collection\Collection;

class PropelEngine implements IDatabaseEngine
{
    public function __construct()
    {
    }

    public function connect(array $config)
    {
        $host     = $config['host'];
        $dbname   = $config['dbname'];
        $user     = $config['uname'];
        $password = $config['pword'];

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

    public function deleteAccountById(int $id): int
    {
        $query    = AccountQuery::create();
        $resource = $query->findOneById($id);
        if (null === $resource) {
            return 0;
        }
        $resource->delete();

        return $id;
    }


    public function getClassroomById(int $id): array
    {
        $query    = new ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return [];
        }
        $c = $resource->getCreator();

        return $resource->toArray();
    }


    /**
     * @return array|Classroom[]|Collection
     */
    public function getClassrooms(): Classroom|Collection
    {
        return ClassroomQuery::create()->find();
    }

    /**
     * @param  array  $body
     *
     * @return array
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function postClassroom(array $body): array
    {
        $resource = new Classroom();
        $resource->setLabel($body['Label']);
        $resource->setOrdering($body['Ordering']);

        if ($body['Ordering']) {
            $classrooms = ClassroomQuery::create()
                                        ->filterByOrdering(['min' => $body['Ordering']])
                                        ->orderBy('Ordering', Criteria::DESC)
                                        ->find();

            /** @var Classroom $classroom */
            foreach ($classrooms as $classroom) {
                $classroom->setOrdering($classroom->getOrdering() + 1);
                $classroom->save();
            }
        } else {
            $maxValue = ClassroomQuery::create()
                                      ->withColumn(
                                          'MAX(Ordering)',
                                          'max_ordering'
                                      )
                                      ->select(['max_ordering'])
                                      ->orderBy('max_ordering', Criteria::DESC)
                                      ->findOne();

            $resource->setOrdering($maxValue + 1);
        }

        $resource->save();

        return $resource->toArray();
    }

    public function putClassroomById(int $id, array $body): ?array
    {
        $query    = new ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return null;
        }

        if ($body['Ordering'] > $resource->getOrdering()) {
            // Moving classroom HIGHER in the sort order;
            // Decrease sort order for all classrooms higher than current sort order (exclusive) and lower than new sort order (inclusive)
            $query     = new ClassroomQuery();
            $resources = $query
                ->filterByOrdering(['min' => $resource->getOrdering() + 1, 'max' => $body['Ordering']])
                ->find();
            foreach ($resources as $r) {
                $r->setOrdering($r->getOrdering() - 1);
                $r->save();
            }
        } elseif ($body['Ordering'] < $resource->getOrdering()) {
            // Moving classroom LOWER in the sort order;
            // Increase sort order for all classrooms higher than new sort order (inclusive) and lower than current sort order (exclusive)
            $query     = new ClassroomQuery();
            $resources = $query
                ->filterByOrdering(['min' => $body['Ordering'], 'max' => $resource->getOrdering() - 1])
                ->find();
            foreach ($resources as $r) {
                $r->setOrdering($r->getOrdering() + 1);
                $r->save();
            }
        }

        $resource->setLabel($body['Label']);
        $resource->setOrdering($body['Ordering']);
        $resource->save();

        return $resource->toArray();
    }

    public function deleteClassroomById(int $id): int
    {
        $query    = new ClassroomQuery();
        $resource = $query->findPk($id);
        if (null === $resource) {
            return 0;
        }
        $resource->delete();

        // Decrease by 1 the value of the "ordering" field for all classrooms with an "ordering" value higher
        // than the "ordering" value of the classroom being deleted
        $query = new ClassroomQuery();
        $resources = $query
            ->filterByOrdering([ 'min' => $resource->getOrdering() + 1])
            ->find();

        foreach ($resources as $r) {
            $r->setOrdering($r->getOrdering()-1);
            $r->save();
        }

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

    public function getStudents(): array
    {
        $query    = new StudentQuery();
        $resource = $query->find();

        return $resource->toArray();
    }


    public function postStudent(array $body): array
    {
        $resource = new Student();
        $resource->setFamilyName($body['FamilyName']);
        $resource->setFirstName($body['FirstName']);
        $resource->setEnrolled($body['Enrolled']);
        $resource->setClassroomId($body['ClassroomId']);
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

    public function getSchedules(): array
    {
        $query    = new ScheduleQuery();
        $resource = $query->find();

        return $resource->toArray();
    }

    public function postSchedule(array $body): array
    {
        $resource = new Schedule();
        $resource->setStartDate($body['StartDate']);
        $resource->setSchedule($body['Schedule']);
        $resource->setStudentId($body['StudentId']);
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
