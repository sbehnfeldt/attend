<?php

namespace Attend\Database\attend\Base;

use \Exception;
use \PDO;
use Attend\Database\attend\Schedules as ChildSchedules;
use Attend\Database\attend\SchedulesQuery as ChildSchedulesQuery;
use Attend\Database\attend\Map\SchedulesTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'schedules' table.
 *
 *
 *
 * @method     ChildSchedulesQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildSchedulesQuery orderByStudentId($order = Criteria::ASC) Order by the student_id column
 * @method     ChildSchedulesQuery orderBySchedule($order = Criteria::ASC) Order by the schedule column
 * @method     ChildSchedulesQuery orderByStartDate($order = Criteria::ASC) Order by the start_date column
 * @method     ChildSchedulesQuery orderByEnteredAt($order = Criteria::ASC) Order by the entered_at column
 *
 * @method     ChildSchedulesQuery groupById() Group by the id column
 * @method     ChildSchedulesQuery groupByStudentId() Group by the student_id column
 * @method     ChildSchedulesQuery groupBySchedule() Group by the schedule column
 * @method     ChildSchedulesQuery groupByStartDate() Group by the start_date column
 * @method     ChildSchedulesQuery groupByEnteredAt() Group by the entered_at column
 *
 * @method     ChildSchedulesQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildSchedulesQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildSchedulesQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildSchedulesQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildSchedulesQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildSchedulesQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildSchedulesQuery leftJoinStudents($relationAlias = null) Adds a LEFT JOIN clause to the query using the Students relation
 * @method     ChildSchedulesQuery rightJoinStudents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Students relation
 * @method     ChildSchedulesQuery innerJoinStudents($relationAlias = null) Adds a INNER JOIN clause to the query using the Students relation
 *
 * @method     ChildSchedulesQuery joinWithStudents($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Students relation
 *
 * @method     ChildSchedulesQuery leftJoinWithStudents() Adds a LEFT JOIN clause and with to the query using the Students relation
 * @method     ChildSchedulesQuery rightJoinWithStudents() Adds a RIGHT JOIN clause and with to the query using the Students relation
 * @method     ChildSchedulesQuery innerJoinWithStudents() Adds a INNER JOIN clause and with to the query using the Students relation
 *
 * @method     \Attend\Database\attend\StudentsQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildSchedules findOne(ConnectionInterface $con = null) Return the first ChildSchedules matching the query
 * @method     ChildSchedules findOneOrCreate(ConnectionInterface $con = null) Return the first ChildSchedules matching the query, or a new ChildSchedules object populated from the query conditions when no match is found
 *
 * @method     ChildSchedules findOneById(int $id) Return the first ChildSchedules filtered by the id column
 * @method     ChildSchedules findOneByStudentId(int $student_id) Return the first ChildSchedules filtered by the student_id column
 * @method     ChildSchedules findOneBySchedule(int $schedule) Return the first ChildSchedules filtered by the schedule column
 * @method     ChildSchedules findOneByStartDate(string $start_date) Return the first ChildSchedules filtered by the start_date column
 * @method     ChildSchedules findOneByEnteredAt(int $entered_at) Return the first ChildSchedules filtered by the entered_at column *
 * @method     ChildSchedules requirePk($key, ConnectionInterface $con = null) Return the ChildSchedules by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedules requireOne(ConnectionInterface $con = null) Return the first ChildSchedules matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSchedules requireOneById(int $id) Return the first ChildSchedules filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedules requireOneByStudentId(int $student_id) Return the first ChildSchedules filtered by the student_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedules requireOneBySchedule(int $schedule) Return the first ChildSchedules filtered by the schedule column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedules requireOneByStartDate(string $start_date) Return the first ChildSchedules filtered by the start_date column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildSchedules requireOneByEnteredAt(int $entered_at) Return the first ChildSchedules filtered by the entered_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildSchedules[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildSchedules objects based on current ModelCriteria
 * @method     ChildSchedules[]|ObjectCollection findById(int $id) Return ChildSchedules objects filtered by the id column
 * @method     ChildSchedules[]|ObjectCollection findByStudentId(int $student_id) Return ChildSchedules objects filtered by the student_id column
 * @method     ChildSchedules[]|ObjectCollection findBySchedule(int $schedule) Return ChildSchedules objects filtered by the schedule column
 * @method     ChildSchedules[]|ObjectCollection findByStartDate(string $start_date) Return ChildSchedules objects filtered by the start_date column
 * @method     ChildSchedules[]|ObjectCollection findByEnteredAt(int $entered_at) Return ChildSchedules objects filtered by the entered_at column
 * @method     ChildSchedules[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class SchedulesQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Attend\Database\attend\Base\SchedulesQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\Attend\\Database\\attend\\Schedules',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildSchedulesQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildSchedulesQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildSchedulesQuery) {
            return $criteria;
        }
        $query = new ChildSchedulesQuery();
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
     * @return ChildSchedules|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(SchedulesTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = SchedulesTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([
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
     * @return ChildSchedules A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, student_id, schedule, start_date, entered_at FROM schedules WHERE id = :p0';
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
            /** @var ChildSchedules $obj */
            $obj = new ChildSchedules();
            $obj->hydrate($row);
            SchedulesTableMap::addInstanceToPool($obj,
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
     * @return ChildSchedules|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(SchedulesTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(SchedulesTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id[ 'min' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_ID, $id[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id[ 'max' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_ID, $id[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SchedulesTableMap::COL_ID, $id, $comparison);
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
     * @see       filterByStudents()
     *
     * @param     mixed $studentId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterByStudentId($studentId = null, $comparison = null)
    {
        if (is_array($studentId)) {
            $useMinMax = false;
            if (isset($studentId[ 'min' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_STUDENT_ID, $studentId[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($studentId[ 'max' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_STUDENT_ID, $studentId[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SchedulesTableMap::COL_STUDENT_ID, $studentId, $comparison);
    }

    /**
     * Filter the query on the schedule column
     *
     * Example usage:
     * <code>
     * $query->filterBySchedule(1234); // WHERE schedule = 1234
     * $query->filterBySchedule(array(12, 34)); // WHERE schedule IN (12, 34)
     * $query->filterBySchedule(array('min' => 12)); // WHERE schedule > 12
     * </code>
     *
     * @param     mixed $schedule The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterBySchedule($schedule = null, $comparison = null)
    {
        if (is_array($schedule)) {
            $useMinMax = false;
            if (isset($schedule[ 'min' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_SCHEDULE, $schedule[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($schedule[ 'max' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_SCHEDULE, $schedule[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SchedulesTableMap::COL_SCHEDULE, $schedule, $comparison);
    }

    /**
     * Filter the query on the start_date column
     *
     * Example usage:
     * <code>
     * $query->filterByStartDate('2011-03-14'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate('now'); // WHERE start_date = '2011-03-14'
     * $query->filterByStartDate(array('max' => 'yesterday')); // WHERE start_date > '2011-03-13'
     * </code>
     *
     * @param     mixed $startDate The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterByStartDate($startDate = null, $comparison = null)
    {
        if (is_array($startDate)) {
            $useMinMax = false;
            if (isset($startDate[ 'min' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_START_DATE, $startDate[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($startDate[ 'max' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_START_DATE, $startDate[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SchedulesTableMap::COL_START_DATE, $startDate, $comparison);
    }

    /**
     * Filter the query on the entered_at column
     *
     * Example usage:
     * <code>
     * $query->filterByEnteredAt(1234); // WHERE entered_at = 1234
     * $query->filterByEnteredAt(array(12, 34)); // WHERE entered_at IN (12, 34)
     * $query->filterByEnteredAt(array('min' => 12)); // WHERE entered_at > 12
     * </code>
     *
     * @param     mixed $enteredAt The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterByEnteredAt($enteredAt = null, $comparison = null)
    {
        if (is_array($enteredAt)) {
            $useMinMax = false;
            if (isset($enteredAt[ 'min' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_ENTERED_AT, $enteredAt[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($enteredAt[ 'max' ])) {
                $this->addUsingAlias(SchedulesTableMap::COL_ENTERED_AT, $enteredAt[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(SchedulesTableMap::COL_ENTERED_AT, $enteredAt, $comparison);
    }

    /**
     * Filter the query by a related \Attend\Database\attend\Students object
     *
     * @param \Attend\Database\attend\Students|ObjectCollection $students The related object(s) to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildSchedulesQuery The current query, for fluid interface
     */
    public function filterByStudents($students, $comparison = null)
    {
        if ($students instanceof \Attend\Database\attend\Students) {
            return $this
                ->addUsingAlias(SchedulesTableMap::COL_STUDENT_ID, $students->getId(), $comparison);
        } elseif ($students instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            return $this
                ->addUsingAlias(SchedulesTableMap::COL_STUDENT_ID, $students->toKeyValue('PrimaryKey', 'Id'),
                    $comparison);
        } else {
            throw new PropelException('filterByStudents() only accepts arguments of type \Attend\Database\attend\Students or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Students relation
     *
     * @param     string $relationAlias optional alias for the relation
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function joinStudents($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Students');

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
            $this->addJoinObject($join, 'Students');
        }

        return $this;
    }

    /**
     * Use the Students relation Students object
     *
     * @see useQuery()
     *
     * @param     string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param     string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \Attend\Database\attend\StudentsQuery A secondary query class using the current class as primary query
     */
    public function useStudentsQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinStudents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Students', '\Attend\Database\attend\StudentsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildSchedules $schedules Object to remove from the list of results
     *
     * @return $this|ChildSchedulesQuery The current query, for fluid interface
     */
    public function prune($schedules = null)
    {
        if ($schedules) {
            $this->addUsingAlias(SchedulesTableMap::COL_ID, $schedules->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the schedules table.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SchedulesTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            SchedulesTableMap::clearInstancePool();
            SchedulesTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(SchedulesTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(SchedulesTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            SchedulesTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            SchedulesTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // SchedulesQuery
