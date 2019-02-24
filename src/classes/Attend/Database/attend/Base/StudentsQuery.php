<?php

namespace Attend\Database\attend\Base;

use \Exception;
use \PDO;
use Attend\Database\attend\Students as ChildStudents;
use Attend\Database\attend\StudentsQuery as ChildStudentsQuery;
use Attend\Database\attend\Map\StudentsTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'students' table.
 *
 *
 *
 * @method     ChildStudentsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildStudentsQuery orderByFamilyName($order = Criteria::ASC) Order by the family_name column
 * @method     ChildStudentsQuery orderByFirstName($order = Criteria::ASC) Order by the first_name column
 * @method     ChildStudentsQuery orderByEnrolled($order = Criteria::ASC) Order by the enrolled column
 * @method     ChildStudentsQuery orderByClassroomId($order = Criteria::ASC) Order by the classroom_id column
 *
 * @method     ChildStudentsQuery groupById() Group by the id column
 * @method     ChildStudentsQuery groupByFamilyName() Group by the family_name column
 * @method     ChildStudentsQuery groupByFirstName() Group by the first_name column
 * @method     ChildStudentsQuery groupByEnrolled() Group by the enrolled column
 * @method     ChildStudentsQuery groupByClassroomId() Group by the classroom_id column
 *
 * @method     ChildStudentsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildStudentsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildStudentsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildStudentsQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildStudentsQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildStudentsQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildStudentsQuery leftJoinClassrooms($relationAlias = null) Adds a LEFT JOIN clause to the query using the Classrooms relation
 * @method     ChildStudentsQuery rightJoinClassrooms($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Classrooms relation
 * @method     ChildStudentsQuery innerJoinClassrooms($relationAlias = null) Adds a INNER JOIN clause to the query using the Classrooms relation
 *
 * @method     ChildStudentsQuery joinWithClassrooms($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Classrooms relation
 *
 * @method     ChildStudentsQuery leftJoinWithClassrooms() Adds a LEFT JOIN clause and with to the query using the Classrooms relation
 * @method     ChildStudentsQuery rightJoinWithClassrooms() Adds a RIGHT JOIN clause and with to the query using the Classrooms relation
 * @method     ChildStudentsQuery innerJoinWithClassrooms() Adds a INNER JOIN clause and with to the query using the Classrooms relation
 *
 * @method     ChildStudentsQuery leftJoinAttendance($relationAlias = null) Adds a LEFT JOIN clause to the query using the Attendance relation
 * @method     ChildStudentsQuery rightJoinAttendance($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Attendance relation
 * @method     ChildStudentsQuery innerJoinAttendance($relationAlias = null) Adds a INNER JOIN clause to the query using the Attendance relation
 *
 * @method     ChildStudentsQuery joinWithAttendance($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Attendance relation
 *
 * @method     ChildStudentsQuery leftJoinWithAttendance() Adds a LEFT JOIN clause and with to the query using the Attendance relation
 * @method     ChildStudentsQuery rightJoinWithAttendance() Adds a RIGHT JOIN clause and with to the query using the Attendance relation
 * @method     ChildStudentsQuery innerJoinWithAttendance() Adds a INNER JOIN clause and with to the query using the Attendance relation
 *
 * @method     ChildStudentsQuery leftJoinSchedules($relationAlias = null) Adds a LEFT JOIN clause to the query using the Schedules relation
 * @method     ChildStudentsQuery rightJoinSchedules($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Schedules relation
 * @method     ChildStudentsQuery innerJoinSchedules($relationAlias = null) Adds a INNER JOIN clause to the query using the Schedules relation
 *
 * @method     ChildStudentsQuery joinWithSchedules($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Schedules relation
 *
 * @method     ChildStudentsQuery leftJoinWithSchedules() Adds a LEFT JOIN clause and with to the query using the Schedules relation
 * @method     ChildStudentsQuery rightJoinWithSchedules() Adds a RIGHT JOIN clause and with to the query using the Schedules relation
 * @method     ChildStudentsQuery innerJoinWithSchedules() Adds a INNER JOIN clause and with to the query using the Schedules relation
 *
 * @method     \Attend\Database\attend\ClassroomsQuery|\Attend\Database\attend\AttendanceQuery|\Attend\Database\attend\SchedulesQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildStudents findOne(ConnectionInterface $con = null) Return the first ChildStudents matching the query
 * @method     ChildStudents findOneOrCreate(ConnectionInterface $con = null) Return the first ChildStudents matching the query, or a new ChildStudents object populated from the query conditions when no match is found
 *
 * @method     ChildStudents findOneById(int $id) Return the first ChildStudents filtered by the id column
 * @method     ChildStudents findOneByFamilyName(string $family_name) Return the first ChildStudents filtered by the family_name column
 * @method     ChildStudents findOneByFirstName(string $first_name) Return the first ChildStudents filtered by the first_name column
 * @method     ChildStudents findOneByEnrolled(int $enrolled) Return the first ChildStudents filtered by the enrolled column
 * @method     ChildStudents findOneByClassroomId(int $classroom_id) Return the first ChildStudents filtered by the classroom_id column *
 * @method     ChildStudents requirePk($key, ConnectionInterface $con = null) Return the ChildStudents by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudents requireOne(ConnectionInterface $con = null) Return the first ChildStudents matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStudents requireOneById(int $id) Return the first ChildStudents filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudents requireOneByFamilyName(string $family_name) Return the first ChildStudents filtered by the family_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudents requireOneByFirstName(string $first_name) Return the first ChildStudents filtered by the first_name column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudents requireOneByEnrolled(int $enrolled) Return the first ChildStudents filtered by the enrolled column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildStudents requireOneByClassroomId(int $classroom_id) Return the first ChildStudents filtered by the classroom_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildStudents[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildStudents objects based on current ModelCriteria
 * @method     ChildStudents[]|ObjectCollection findById(int $id) Return ChildStudents objects filtered by the id column
 * @method     ChildStudents[]|ObjectCollection findByFamilyName(string $family_name) Return ChildStudents objects filtered by the family_name column
 * @method     ChildStudents[]|ObjectCollection findByFirstName(string $first_name) Return ChildStudents objects filtered by the first_name column
 * @method     ChildStudents[]|ObjectCollection findByEnrolled(int $enrolled) Return ChildStudents objects filtered by the enrolled column
 * @method     ChildStudents[]|ObjectCollection findByClassroomId(int $classroom_id) Return ChildStudents objects filtered by the classroom_id column
 * @method     ChildStudents[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class StudentsQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Attend\Database\attend\Base\StudentsQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\Attend\\Database\\attend\\Students',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildStudentsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildStudentsQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildStudentsQuery) {
            return $criteria;
        }
        $query = new ChildStudentsQuery();
        if (null !== $modelAlias) {
            $query->setModelAlias($modelAlias);
        }
        if ($criteria instanceof Criteria) {
            $query->mergeWith($criteria);
        }

        return $query;
    }

    /**
     * Find object by primary key.
     * Propel uses the instance pool to skip the database if the object exists.
     * Go fast if the query is untouched.
     *
     * <code>
     * $obj  = $c->findPk(12, $con);
     * </code>
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ChildStudents|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StudentsTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = StudentsTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([
                $key,
                '__toString'
            ]) ? (string)$key : $key)))
        ) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildStudents A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, family_name, first_name, enrolled, classroom_id FROM students WHERE id = :p0';
        try {
            $stmt = $con->prepare($sql);
            $stmt->bindValue(':p0', $key, PDO::PARAM_INT);
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute SELECT statement [%s]', $sql), 0, $e);
        }
        $obj = null;
        if ($row = $stmt->fetch(\PDO::FETCH_NUM)) {
            /** @var ChildStudents $obj */
            $obj = new ChildStudents();
            $obj->hydrate($row);
            StudentsTableMap::addInstanceToPool($obj,
                null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string)$key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param     mixed $key Primary key to use for the query
     * @param     ConnectionInterface $con A connection object
     *
     * @return ChildStudents|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria    = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKey($key)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->formatOne($dataFetcher);
    }

    /**
     * Find objects by primary key
     * <code>
     * $objs = $c->findPks(array(12, 56, 832), $con);
     * </code>
     *
     * @param     array $keys Primary keys to use for the query
     * @param     ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria    = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param     mixed $key Primary key to use for the query
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(StudentsTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(StudentsTableMap::COL_ID, $keys, Criteria::IN);
    }

    /**
     * Filter the query on the id column
     *
     * Example usage:
     * <code>
     * $query->filterById(1234); // WHERE id = 1234
     * $query->filterById(array(12, 34)); // WHERE id IN (12, 34)
     * $query->filterById(array('min' => 12)); // WHERE id > 12
     * </code>
     *
     * @param     mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id[ 'min' ])) {
                $this->addUsingAlias(StudentsTableMap::COL_ID, $id[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id[ 'max' ])) {
                $this->addUsingAlias(StudentsTableMap::COL_ID, $id[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentsTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the family_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFamilyName('fooValue');   // WHERE family_name = 'fooValue'
     * $query->filterByFamilyName('%fooValue%', Criteria::LIKE); // WHERE family_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $familyName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByFamilyName($familyName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($familyName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentsTableMap::COL_FAMILY_NAME, $familyName, $comparison);
    }

    /**
     * Filter the query on the first_name column
     *
     * Example usage:
     * <code>
     * $query->filterByFirstName('fooValue');   // WHERE first_name = 'fooValue'
     * $query->filterByFirstName('%fooValue%', Criteria::LIKE); // WHERE first_name LIKE '%fooValue%'
     * </code>
     *
     * @param     string $firstName The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByFirstName($firstName = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($firstName)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentsTableMap::COL_FIRST_NAME, $firstName, $comparison);
    }

    /**
     * Filter the query on the enrolled column
     *
     * Example usage:
     * <code>
     * $query->filterByEnrolled(1234); // WHERE enrolled = 1234
     * $query->filterByEnrolled(array(12, 34)); // WHERE enrolled IN (12, 34)
     * $query->filterByEnrolled(array('min' => 12)); // WHERE enrolled > 12
     * </code>
     *
     * @param     mixed $enrolled The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByEnrolled($enrolled = null, $comparison = null)
    {
        if (is_array($enrolled)) {
            $useMinMax = false;
            if (isset($enrolled[ 'min' ])) {
                $this->addUsingAlias(StudentsTableMap::COL_ENROLLED, $enrolled[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enrolled[ 'max' ])) {
                $this->addUsingAlias(StudentsTableMap::COL_ENROLLED, $enrolled[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentsTableMap::COL_ENROLLED, $enrolled, $comparison);
    }

    /**
     * Filter the query on the classroom_id column
     *
     * Example usage:
     * <code>
     * $query->filterByClassroomId(1234); // WHERE classroom_id = 1234
     * $query->filterByClassroomId(array(12, 34)); // WHERE classroom_id IN (12, 34)
     * $query->filterByClassroomId(array('min' => 12)); // WHERE classroom_id > 12
     * </code>
     *
     * @see       filterByClassrooms()
     *
     * @param     mixed $classroomId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByClassroomId($classroomId = null, $comparison = null)
    {
        if (is_array($classroomId)) {
            $useMinMax = false;
            if (isset($classroomId[ 'min' ])) {
                $this->addUsingAlias(StudentsTableMap::COL_CLASSROOM_ID, $classroomId[ 'min' ],
                    Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($classroomId[ 'max' ])) {
                $this->addUsingAlias(StudentsTableMap::COL_CLASSROOM_ID, $classroomId[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(StudentsTableMap::COL_CLASSROOM_ID, $classroomId, $comparison);
    }

    /**
     * Filter the query by a related \Attend\Database\attend\Classrooms object
     *
     * @param \Attend\Database\attend\Classrooms|ObjectCollection $classrooms The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByClassrooms($classrooms, $comparison = null)
    {
        if ($classrooms instanceof \Attend\Database\attend\Classrooms) {
            return $this
                ->addUsingAlias(StudentsTableMap::COL_CLASSROOM_ID, $classrooms->getId(), $comparison);
        } elseif ($classrooms instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(StudentsTableMap::COL_CLASSROOM_ID, $classrooms->toKeyValue('PrimaryKey', 'Id'),
                    $comparison);
        } else {
            throw new PropelException('filterByClassrooms() only accepts arguments of type \Attend\Database\attend\Classrooms or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Classrooms relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function joinClassrooms($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Classrooms');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Classrooms');
        }

        return $this;
    }

    /**
     * Use the Classrooms relation Classrooms object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\attend\ClassroomsQuery A secondary query class using the current class as primary query
     */
    public function useClassroomsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinClassrooms($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Classrooms', '\Attend\Database\attend\ClassroomsQuery');
    }

    /**
     * Filter the query by a related \Attend\Database\attend\Attendance object
     *
     * @param \Attend\Database\attend\Attendance|ObjectCollection $attendance the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStudentsQuery The current query, for fluid interface
     */
    public function filterByAttendance($attendance, $comparison = null)
    {
        if ($attendance instanceof \Attend\Database\attend\Attendance) {
            return $this
                ->addUsingAlias(StudentsTableMap::COL_ID, $attendance->getStudentId(), $comparison);
        } elseif ($attendance instanceof ObjectCollection) {
            return $this
                ->useAttendanceQuery()
                ->filterByPrimaryKeys($attendance->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterByAttendance() only accepts arguments of type \Attend\Database\attend\Attendance or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Attendance relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function joinAttendance($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Attendance');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Attendance');
        }

        return $this;
    }

    /**
     * Use the Attendance relation Attendance object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\attend\AttendanceQuery A secondary query class using the current class as primary query
     */
    public function useAttendanceQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAttendance($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Attendance', '\Attend\Database\attend\AttendanceQuery');
    }

    /**
     * Filter the query by a related \Attend\Database\attend\Schedules object
     *
     * @param \Attend\Database\attend\Schedules|ObjectCollection $schedules the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildStudentsQuery The current query, for fluid interface
     */
    public function filterBySchedules($schedules, $comparison = null)
    {
        if ($schedules instanceof \Attend\Database\attend\Schedules) {
            return $this
                ->addUsingAlias(StudentsTableMap::COL_ID, $schedules->getStudentId(), $comparison);
        } elseif ($schedules instanceof ObjectCollection) {
            return $this
                ->useSchedulesQuery()
                ->filterByPrimaryKeys($schedules->getPrimaryKeys())
                ->endUse();
        } else {
            throw new PropelException('filterBySchedules() only accepts arguments of type \Attend\Database\attend\Schedules or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Schedules relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function joinSchedules($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Schedules');

        // create a ModelJoin object for this join
        $join = new ModelJoin();
        $join->setJoinType($joinType);
        $join->setRelationMap($relationMap, $this->useAliasInSQL ? $this->getModelAlias() : null, $relationAlias);
        if ($previousJoin = $this->getPreviousJoin()) {
            $join->setPreviousJoin($previousJoin);
        }

        // add the ModelJoin to the current object
        if ($relationAlias) {
            $this->addAlias($relationAlias, $relationMap->getRightTable()->getName());
            $this->addJoinObject($join, $relationAlias);
        } else {
            $this->addJoinObject($join, 'Schedules');
        }

        return $this;
    }

    /**
     * Use the Schedules relation Schedules object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\attend\SchedulesQuery A secondary query class using the current class as primary query
     */
    public function useSchedulesQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinSchedules($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Schedules', '\Attend\Database\attend\SchedulesQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildStudents $students Object to remove from the list of results
     *
     * @return $this|ChildStudentsQuery The current query, for fluid interface
     */
    public function prune($students = null)
    {
        if ($students) {
            $this->addUsingAlias(StudentsTableMap::COL_ID, $students->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the students table.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentsTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            StudentsTableMap::clearInstancePool();
            StudentsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(StudentsTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            StudentsTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            StudentsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // StudentsQuery
