<?php

namespace flapjack\attend\database\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;
use flapjack\attend\database\Attendance as ChildAttendance;
use flapjack\attend\database\AttendanceQuery as ChildAttendanceQuery;
use flapjack\attend\database\Map\AttendanceTableMap;

/**
 * Base class that represents a query for the `attendance` table.
 *
 * @method     ChildAttendanceQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildAttendanceQuery orderByStudentId($order = Criteria::ASC) Order by the student_id column
 * @method     ChildAttendanceQuery orderByCheckIn($order = Criteria::ASC) Order by the check_in column
 * @method     ChildAttendanceQuery orderByCheckOut($order = Criteria::ASC) Order by the check_out column
 *
 * @method     ChildAttendanceQuery groupById() Group by the id column
 * @method     ChildAttendanceQuery groupByStudentId() Group by the student_id column
 * @method     ChildAttendanceQuery groupByCheckIn() Group by the check_in column
 * @method     ChildAttendanceQuery groupByCheckOut() Group by the check_out column
 *
 * @method     ChildAttendanceQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAttendanceQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAttendanceQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAttendanceQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildAttendanceQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildAttendanceQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildAttendanceQuery leftJoinStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Student relation
 * @method     ChildAttendanceQuery rightJoinStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Student relation
 * @method     ChildAttendanceQuery innerJoinStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the Student relation
 *
 * @method     ChildAttendanceQuery joinWithStudent($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Student relation
 *
 * @method     ChildAttendanceQuery leftJoinWithStudent() Adds a LEFT JOIN clause and with to the query using the Student relation
 * @method     ChildAttendanceQuery rightJoinWithStudent() Adds a RIGHT JOIN clause and with to the query using the Student relation
 * @method     ChildAttendanceQuery innerJoinWithStudent() Adds a INNER JOIN clause and with to the query using the Student relation
 *
 * @method     \flapjack\attend\database\StudentQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildAttendance|null findOne(?ConnectionInterface $con = null) Return the first ChildAttendance matching the query
 * @method     ChildAttendance findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildAttendance matching the query, or a new ChildAttendance object populated from the query conditions when no match is found
 *
 * @method     ChildAttendance|null findOneById(int $id) Return the first ChildAttendance filtered by the id column
 * @method     ChildAttendance|null findOneByStudentId(int $student_id) Return the first ChildAttendance filtered by the student_id column
 * @method     ChildAttendance|null findOneByCheckIn(string $check_in) Return the first ChildAttendance filtered by the check_in column
 * @method     ChildAttendance|null findOneByCheckOut(string $check_out) Return the first ChildAttendance filtered by the check_out column
 *
 * @method     ChildAttendance requirePk($key, ?ConnectionInterface $con = null) Return the ChildAttendance by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAttendance requireOne(?ConnectionInterface $con = null) Return the first ChildAttendance matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildAttendance requireOneById(int $id) Return the first ChildAttendance filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAttendance requireOneByStudentId(int $student_id) Return the first ChildAttendance filtered by the student_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAttendance requireOneByCheckIn(string $check_in) Return the first ChildAttendance filtered by the check_in column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAttendance requireOneByCheckOut(string $check_out) Return the first ChildAttendance filtered by the check_out column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildAttendance[]|Collection find(?ConnectionInterface $con = null) Return ChildAttendance objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildAttendance> find(?ConnectionInterface $con = null) Return ChildAttendance objects based on current ModelCriteria
 *
 * @method     ChildAttendance[]|Collection findById(int|array<int> $id) Return ChildAttendance objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildAttendance> findById(int|array<int> $id) Return ChildAttendance objects filtered by the id column
 * @method     ChildAttendance[]|Collection findByStudentId(int|array<int> $student_id) Return ChildAttendance objects filtered by the student_id column
 * @psalm-method Collection&\Traversable<ChildAttendance> findByStudentId(int|array<int> $student_id) Return ChildAttendance objects filtered by the student_id column
 * @method     ChildAttendance[]|Collection findByCheckIn(string|array<string> $check_in) Return ChildAttendance objects filtered by the check_in column
 * @psalm-method Collection&\Traversable<ChildAttendance> findByCheckIn(string|array<string> $check_in) Return ChildAttendance objects filtered by the check_in column
 * @method     ChildAttendance[]|Collection findByCheckOut(string|array<string> $check_out) Return ChildAttendance objects filtered by the check_out column
 * @psalm-method Collection&\Traversable<ChildAttendance> findByCheckOut(string|array<string> $check_out) Return ChildAttendance objects filtered by the check_out column
 *
 * @method     ChildAttendance[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildAttendance> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class AttendanceQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\AttendanceQuery object.
     *
     * @param  string  $dbName  The database name
     * @param  string  $modelName  The phpName of a model, e.g. 'Book'
     * @param  string  $modelAlias  The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\flapjack\\attend\\database\\Attendance',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAttendanceQuery object.
     *
     * @param  string  $modelAlias  The alias of a model in the query
     * @param  Criteria  $criteria  Optional Criteria to build the query from
     *
     * @return ChildAttendanceQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildAttendanceQuery) {
            return $criteria;
        }
        $query = new ChildAttendanceQuery();
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
     * @return ChildAttendance|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AttendanceTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = AttendanceTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param  mixed  $key  Primary key to use for the query
     * @param  ConnectionInterface  $con  A connection object
     *
     * @return ChildAttendance A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, student_id, check_in, check_out FROM attendance WHERE id = :p0';
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
            /** @var ChildAttendance $obj */
            $obj = new ChildAttendance();
            $obj->hydrate($row);
            AttendanceTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param  mixed  $key  Primary key to use for the query
     * @param  ConnectionInterface  $con  A connection object
     *
     * @return ChildAttendance|array|mixed the result, formatted by the current formatter
     */
    protected function findPkComplex($key, ConnectionInterface $con)
    {
        // As the query uses a PK condition, no limit(1) is necessary.
        $criteria = $this->isKeepQuery() ? clone $this : $this;
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
     * @param  array  $keys  Primary keys to use for the query
     * @param  ConnectionInterface  $con  an optional connection object
     *
     * @return Collection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ?ConnectionInterface $con = null)
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
     * @param  mixed  $key  Primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        $this->addUsingAlias(AttendanceTableMap::COL_ID, $key, Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param  array|int  $keys  The list of primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        $this->addUsingAlias(AttendanceTableMap::COL_ID, $keys, Criteria::IN);

        return $this;
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
     * @param  mixed  $id  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterById($id = null, ?string $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AttendanceTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the student_id column
     *
     * Example usage:
     * <code>
     * $query->filterByStudentId(1234); // WHERE student_id = 1234
     * $query->filterByStudentId(array(12, 34)); // WHERE student_id IN (12, 34)
     * $query->filterByStudentId(array('min' => 12)); // WHERE student_id > 12
     * </code>
     *
     * @see       filterByStudent()
     *
     * @param  mixed  $studentId  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByStudentId($studentId = null, ?string $comparison = null)
    {
        if (is_array($studentId)) {
            $useMinMax = false;
            if (isset($studentId['min'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_STUDENT_ID, $studentId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentId['max'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_STUDENT_ID, $studentId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AttendanceTableMap::COL_STUDENT_ID, $studentId, $comparison);

        return $this;
    }

    /**
     * Filter the query on the check_in column
     *
     * Example usage:
     * <code>
     * $query->filterByCheckIn('2011-03-14'); // WHERE check_in = '2011-03-14'
     * $query->filterByCheckIn('now'); // WHERE check_in = '2011-03-14'
     * $query->filterByCheckIn(array('max' => 'yesterday')); // WHERE check_in > '2011-03-13'
     * </code>
     *
     * @param  mixed  $checkIn  The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByCheckIn($checkIn = null, ?string $comparison = null)
    {
        if (is_array($checkIn)) {
            $useMinMax = false;
            if (isset($checkIn['min'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_CHECK_IN, $checkIn['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($checkIn['max'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_CHECK_IN, $checkIn['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AttendanceTableMap::COL_CHECK_IN, $checkIn, $comparison);

        return $this;
    }

    /**
     * Filter the query on the check_out column
     *
     * Example usage:
     * <code>
     * $query->filterByCheckOut('2011-03-14'); // WHERE check_out = '2011-03-14'
     * $query->filterByCheckOut('now'); // WHERE check_out = '2011-03-14'
     * $query->filterByCheckOut(array('max' => 'yesterday')); // WHERE check_out > '2011-03-13'
     * </code>
     *
     * @param  mixed  $checkOut  The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByCheckOut($checkOut = null, ?string $comparison = null)
    {
        if (is_array($checkOut)) {
            $useMinMax = false;
            if (isset($checkOut['min'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_CHECK_OUT, $checkOut['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($checkOut['max'])) {
                $this->addUsingAlias(AttendanceTableMap::COL_CHECK_OUT, $checkOut['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AttendanceTableMap::COL_CHECK_OUT, $checkOut, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Student object
     *
     * @param  \flapjack\attend\database\Student|ObjectCollection  $student  The related object(s) to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByStudent($student, ?string $comparison = null)
    {
        if ($student instanceof \flapjack\attend\database\Student) {
            return $this
                ->addUsingAlias(AttendanceTableMap::COL_STUDENT_ID, $student->getId(), $comparison);
        } elseif ($student instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(
                    AttendanceTableMap::COL_STUDENT_ID,
                    $student->toKeyValue('PrimaryKey', 'Id'),
                    $comparison
                );

            return $this;
        } else {
            throw new PropelException(
                'filterByStudent() only accepts arguments of type \flapjack\attend\database\Student or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the Student relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinStudent(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Student');

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
            $this->addJoinObject($join, 'Student');
        }

        return $this;
    }

    /**
     * Use the Student relation Student object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\StudentQuery A secondary query class using the current class as primary query
     */
    public function useStudentQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStudent($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Student', '\flapjack\attend\database\StudentQuery');
    }

    /**
     * Use the Student relation Student object
     *
     * @param  callable(\flapjack\attend\database\StudentQuery):\flapjack\attend\database\StudentQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withStudentQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useStudentQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Student table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the EXISTS statement
     */
    public function useStudentExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useExistsQuery('Student', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Student table for a NOT EXISTS query.
     *
     * @see useStudentExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the NOT EXISTS statement
     */
    public function useStudentNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useExistsQuery('Student', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Student table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the IN statement
     */
    public function useInStudentQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useInQuery('Student', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Student table for a NOT IN query.
     *
     * @see useStudentInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\StudentQuery The inner query object of the NOT IN statement
     */
    public function useNotInStudentQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\StudentQuery */
        $q = $this->useInQuery('Student', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param  ChildAttendance  $attendance  Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($attendance = null)
    {
        if ($attendance) {
            $this->addUsingAlias(AttendanceTableMap::COL_ID, $attendance->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the attendance table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttendanceTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AttendanceTableMap::clearInstancePool();
            AttendanceTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param  ConnectionInterface  $con  the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AttendanceTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AttendanceTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            AttendanceTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            AttendanceTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
