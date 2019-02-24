<?php

namespace Attend\Database\attend\Base;

use \Exception;
use \PDO;
use Attend\Database\attend\Classrooms as ChildClassrooms;
use Attend\Database\attend\ClassroomsQuery as ChildClassroomsQuery;
use Attend\Database\attend\Map\ClassroomsTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveQuery\ModelJoin;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'classrooms' table.
 *
 *
 *
 * @method     ChildClassroomsQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildClassroomsQuery orderByLabel($order = Criteria::ASC) Order by the label column
 * @method     ChildClassroomsQuery orderByOrdering($order = Criteria::ASC) Order by the ordering column
 * @method     ChildClassroomsQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildClassroomsQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildClassroomsQuery groupById() Group by the id column
 * @method     ChildClassroomsQuery groupByLabel() Group by the label column
 * @method     ChildClassroomsQuery groupByOrdering() Group by the ordering column
 * @method     ChildClassroomsQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildClassroomsQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildClassroomsQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildClassroomsQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildClassroomsQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildClassroomsQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildClassroomsQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildClassroomsQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildClassroomsQuery leftJoinStudents($relationAlias = null) Adds a LEFT JOIN clause to the query using the Students relation
 * @method     ChildClassroomsQuery rightJoinStudents($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Students relation
 * @method     ChildClassroomsQuery innerJoinStudents($relationAlias = null) Adds a INNER JOIN clause to the query using the Students relation
 *
 * @method     ChildClassroomsQuery joinWithStudents($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Students relation
 *
 * @method     ChildClassroomsQuery leftJoinWithStudents() Adds a LEFT JOIN clause and with to the query using the Students relation
 * @method     ChildClassroomsQuery rightJoinWithStudents() Adds a RIGHT JOIN clause and with to the query using the Students relation
 * @method     ChildClassroomsQuery innerJoinWithStudents() Adds a INNER JOIN clause and with to the query using the Students relation
 *
 * @method     \Attend\Database\attend\StudentsQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildClassrooms findOne(ConnectionInterface $con = null) Return the first ChildClassrooms matching the query
 * @method     ChildClassrooms findOneOrCreate(ConnectionInterface $con = null) Return the first ChildClassrooms matching the query, or a new ChildClassrooms object populated from the query conditions when no match is found
 *
 * @method     ChildClassrooms findOneById(int $id) Return the first ChildClassrooms filtered by the id column
 * @method     ChildClassrooms findOneByLabel(string $label) Return the first ChildClassrooms filtered by the label column
 * @method     ChildClassrooms findOneByOrdering(int $ordering) Return the first ChildClassrooms filtered by the ordering column
 * @method     ChildClassrooms findOneByCreatedAt(string $created_at) Return the first ChildClassrooms filtered by the created_at column
 * @method     ChildClassrooms findOneByUpdatedAt(string $updated_at) Return the first ChildClassrooms filtered by the updated_at column *
 * @method     ChildClassrooms requirePk($key, ConnectionInterface $con = null) Return the ChildClassrooms by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassrooms requireOne(ConnectionInterface $con = null) Return the first ChildClassrooms matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildClassrooms requireOneById(int $id) Return the first ChildClassrooms filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassrooms requireOneByLabel(string $label) Return the first ChildClassrooms filtered by the label column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassrooms requireOneByOrdering(int $ordering) Return the first ChildClassrooms filtered by the ordering column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassrooms requireOneByCreatedAt(string $created_at) Return the first ChildClassrooms filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassrooms requireOneByUpdatedAt(string $updated_at) Return the first ChildClassrooms filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildClassrooms[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildClassrooms objects based on current ModelCriteria
 * @method     ChildClassrooms[]|ObjectCollection findById(int $id) Return ChildClassrooms objects filtered by the id column
 * @method     ChildClassrooms[]|ObjectCollection findByLabel(string $label) Return ChildClassrooms objects filtered by the label column
 * @method     ChildClassrooms[]|ObjectCollection findByOrdering(int $ordering) Return ChildClassrooms objects filtered by the ordering column
 * @method     ChildClassrooms[]|ObjectCollection findByCreatedAt(string $created_at) Return ChildClassrooms objects filtered by the created_at column
 * @method     ChildClassrooms[]|ObjectCollection findByUpdatedAt(string $updated_at) Return ChildClassrooms objects filtered by the updated_at column
 * @method     ChildClassrooms[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class ClassroomsQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Attend\Database\attend\Base\ClassroomsQuery object.
     *
     * @param     string $dbName The database name
     * @param     string $modelName The phpName of a model, e.g. 'Book'
     * @param     string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\Attend\\Database\\attend\\Classrooms',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildClassroomsQuery object.
     *
     * @param     string $modelAlias The alias of a model in the query
     * @param     Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildClassroomsQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildClassroomsQuery) {
            return $criteria;
        }
        $query = new ChildClassroomsQuery();
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
     * @return ChildClassrooms|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ClassroomsTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ClassroomsTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([
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
     * @return ChildClassrooms A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, label, ordering, created_at, updated_at FROM classrooms WHERE id = :p0';
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
            /** @var ChildClassrooms $obj */
            $obj = new ChildClassrooms();
            $obj->hydrate($row);
            ClassroomsTableMap::addInstanceToPool($obj,
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
     * @return ChildClassrooms|array|mixed the result, formatted by the current formatter
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
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(ClassroomsTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param     array $keys The list of primary key to use for the query
     *
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(ClassroomsTableMap::COL_ID, $keys, Criteria::IN);
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
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id[ 'min' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_ID, $id[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id[ 'max' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_ID, $id[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassroomsTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the label column
     *
     * Example usage:
     * <code>
     * $query->filterByLabel('fooValue');   // WHERE label = 'fooValue'
     * $query->filterByLabel('%fooValue%', Criteria::LIKE); // WHERE label LIKE '%fooValue%'
     * </code>
     *
     * @param     string $label The value to use as filter.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByLabel($label = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($label)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassroomsTableMap::COL_LABEL, $label, $comparison);
    }

    /**
     * Filter the query on the ordering column
     *
     * Example usage:
     * <code>
     * $query->filterByOrdering(1234); // WHERE ordering = 1234
     * $query->filterByOrdering(array(12, 34)); // WHERE ordering IN (12, 34)
     * $query->filterByOrdering(array('min' => 12)); // WHERE ordering > 12
     * </code>
     *
     * @param     mixed $ordering The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByOrdering($ordering = null, $comparison = null)
    {
        if (is_array($ordering)) {
            $useMinMax = false;
            if (isset($ordering[ 'min' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_ORDERING, $ordering[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ordering[ 'max' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_ORDERING, $ordering[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassroomsTableMap::COL_ORDERING, $ordering, $comparison);
    }

    /**
     * Filter the query on the created_at column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatedAt('2011-03-14'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt('now'); // WHERE created_at = '2011-03-14'
     * $query->filterByCreatedAt(array('max' => 'yesterday')); // WHERE created_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt[ 'min' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_CREATED_AT, $createdAt[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt[ 'max' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_CREATED_AT, $createdAt[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassroomsTableMap::COL_CREATED_AT, $createdAt, $comparison);
    }

    /**
     * Filter the query on the updated_at column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdatedAt('2011-03-14'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt('now'); // WHERE updated_at = '2011-03-14'
     * $query->filterByUpdatedAt(array('max' => 'yesterday')); // WHERE updated_at > '2011-03-13'
     * </code>
     *
     * @param     mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param     string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt[ 'min' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_UPDATED_AT, $updatedAt[ 'min' ], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt[ 'max' ])) {
                $this->addUsingAlias(ClassroomsTableMap::COL_UPDATED_AT, $updatedAt[ 'max' ], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(ClassroomsTableMap::COL_UPDATED_AT, $updatedAt, $comparison);
    }

    /**
     * Filter the query by a related \Attend\Database\attend\Students object
     *
     * @param \Attend\Database\attend\Students|ObjectCollection $students the related object to use as filter
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return ChildClassroomsQuery The current query, for fluid interface
     */
    public function filterByStudents($students, $comparison = null)
    {
        if ($students instanceof \Attend\Database\attend\Students) {
            return $this
                ->addUsingAlias(ClassroomsTableMap::COL_ID, $students->getClassroomId(), $comparison);
        } elseif ($students instanceof ObjectCollection) {
            return $this
                ->useStudentsQuery()
                ->filterByPrimaryKeys($students->getPrimaryKeys())
                ->endUse();
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
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function joinStudents($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
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
    public function useStudentsQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinStudents($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Students', '\Attend\Database\attend\StudentsQuery');
    }

    /**
     * Exclude object from result
     *
     * @param   ChildClassrooms $classrooms Object to remove from the list of results
     *
     * @return $this|ChildClassroomsQuery The current query, for fluid interface
     */
    public function prune($classrooms = null)
    {
        if ($classrooms) {
            $this->addUsingAlias(ClassroomsTableMap::COL_ID, $classrooms->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the classrooms table.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClassroomsTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ClassroomsTableMap::clearInstancePool();
            ClassroomsTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(ClassroomsTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ClassroomsTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ClassroomsTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ClassroomsTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // ClassroomsQuery
