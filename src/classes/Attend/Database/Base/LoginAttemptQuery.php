<?php

namespace Attend\Database\Base;

use \Exception;
use \PDO;
use Attend\Database\LoginAttempt as ChildLoginAttempt;
use Attend\Database\LoginAttemptQuery as ChildLoginAttemptQuery;
use Attend\Database\Map\LoginAttemptTableMap;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\PropelException;

/**
 * Base class that represents a query for the 'login_attempts' table.
 *
 *
 *
 * @method     ChildLoginAttemptQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildLoginAttemptQuery orderByAttemptedAt($order = Criteria::ASC) Order by the attempted_at column
 * @method     ChildLoginAttemptQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method     ChildLoginAttemptQuery orderByPass($order = Criteria::ASC) Order by the pass column
 *
 * @method     ChildLoginAttemptQuery groupById() Group by the id column
 * @method     ChildLoginAttemptQuery groupByAttemptedAt() Group by the attempted_at column
 * @method     ChildLoginAttemptQuery groupByUsername() Group by the username column
 * @method     ChildLoginAttemptQuery groupByPass() Group by the pass column
 *
 * @method     ChildLoginAttemptQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildLoginAttemptQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildLoginAttemptQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildLoginAttemptQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildLoginAttemptQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildLoginAttemptQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildLoginAttempt findOne(ConnectionInterface $con = null) Return the first ChildLoginAttempt matching the query
 * @method     ChildLoginAttempt findOneOrCreate(ConnectionInterface $con = null) Return the first ChildLoginAttempt matching the query, or a new ChildLoginAttempt object populated from the query conditions when no match is found
 *
 * @method     ChildLoginAttempt findOneById(int $id) Return the first ChildLoginAttempt filtered by the id column
 * @method     ChildLoginAttempt findOneByAttemptedAt(string $attempted_at) Return the first ChildLoginAttempt filtered by the attempted_at column
 * @method     ChildLoginAttempt findOneByUsername(string $username) Return the first ChildLoginAttempt filtered by the username column
 * @method     ChildLoginAttempt findOneByPass(int $pass) Return the first ChildLoginAttempt filtered by the pass column *
 * @method     ChildLoginAttempt requirePk($key, ConnectionInterface $con = null) Return the ChildLoginAttempt by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLoginAttempt requireOne(ConnectionInterface $con = null) Return the first ChildLoginAttempt matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildLoginAttempt requireOneById(int $id) Return the first ChildLoginAttempt filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLoginAttempt requireOneByAttemptedAt(string $attempted_at) Return the first ChildLoginAttempt filtered by the attempted_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLoginAttempt requireOneByUsername(string $username) Return the first ChildLoginAttempt filtered by the username column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildLoginAttempt requireOneByPass(int $pass) Return the first ChildLoginAttempt filtered by the pass column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildLoginAttempt[]|ObjectCollection find(ConnectionInterface $con = null) Return ChildLoginAttempt objects based on current ModelCriteria
 * @method     ChildLoginAttempt[]|ObjectCollection findById(int $id) Return ChildLoginAttempt objects filtered by the id column
 * @method     ChildLoginAttempt[]|ObjectCollection findByAttemptedAt(string $attempted_at) Return ChildLoginAttempt objects filtered by the attempted_at column
 * @method     ChildLoginAttempt[]|ObjectCollection findByUsername(string $username) Return ChildLoginAttempt objects filtered by the username column
 * @method     ChildLoginAttempt[]|ObjectCollection findByPass(int $pass) Return ChildLoginAttempt objects filtered by the pass column
 * @method     ChildLoginAttempt[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 *
 */
abstract class LoginAttemptQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \Attend\Database\Base\LoginAttemptQuery object.
     *
     * @param string $dbName The database name
     * @param string $modelName The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'attend', $modelName = '\\Attend\\Database\\LoginAttempt', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildLoginAttemptQuery object.
     *
     * @param string $modelAlias The alias of a model in the query
     * @param Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildLoginAttemptQuery
     */
    public static function create($modelAlias = null, Criteria $criteria = null)
    {
        if ($criteria instanceof ChildLoginAttemptQuery) {
            return $criteria;
        }
        $query = new ChildLoginAttemptQuery();
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
     * @return ChildLoginAttempt|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(LoginAttemptTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = LoginAttemptTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string)$key : $key)))) {
            // the object is already in the instance pool
            return $obj;
        }

        return $this->findPkSimple($key, $con);
    }

    /**
     * Find object by primary key using raw SQL to go fast.
     * Bypass doSelect() and the object formatter by using generated code.
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildLoginAttempt A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, attempted_at, username, pass FROM login_attempts WHERE id = :p0';
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
            /** @var ChildLoginAttempt $obj */
            $obj = new ChildLoginAttempt();
            $obj->hydrate($row);
            LoginAttemptTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string)$key : $key);
        }
        $stmt->closeCursor();

        return $obj;
    }

    /**
     * Find object by primary key.
     *
     * @param mixed $key Primary key to use for the query
     * @param ConnectionInterface $con A connection object
     *
     * @return ChildLoginAttempt|array|mixed the result, formatted by the current formatter
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
     * @param array $keys Primary keys to use for the query
     * @param ConnectionInterface $con an optional connection object
     *
     * @return ObjectCollection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getReadConnection($this->getDbName());
        }
        $this->basePreSelect($con);
        $criteria = $this->isKeepQuery() ? clone $this : $this;
        $dataFetcher = $criteria
            ->filterByPrimaryKeys($keys)
            ->doSelect($con);

        return $criteria->getFormatter()->init($criteria)->format($dataFetcher);
    }

    /**
     * Filter the query by primary key
     *
     * @param mixed $key Primary key to use for the query
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        return $this->addUsingAlias(LoginAttemptTableMap::COL_ID, $key, Criteria::EQUAL);
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array $keys The list of primary key to use for the query
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        return $this->addUsingAlias(LoginAttemptTableMap::COL_ID, $keys, Criteria::IN);
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
     * @param mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function filterById($id = null, $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(LoginAttemptTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(LoginAttemptTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptTableMap::COL_ID, $id, $comparison);
    }

    /**
     * Filter the query on the attempted_at column
     *
     * Example usage:
     * <code>
     * $query->filterByAttemptedAt('2011-03-14'); // WHERE attempted_at = '2011-03-14'
     * $query->filterByAttemptedAt('now'); // WHERE attempted_at = '2011-03-14'
     * $query->filterByAttemptedAt(array('max' => 'yesterday')); // WHERE attempted_at > '2011-03-13'
     * </code>
     *
     * @param mixed $attemptedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function filterByAttemptedAt($attemptedAt = null, $comparison = null)
    {
        if (is_array($attemptedAt)) {
            $useMinMax = false;
            if (isset($attemptedAt['min'])) {
                $this->addUsingAlias(LoginAttemptTableMap::COL_ATTEMPTED_AT, $attemptedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($attemptedAt['max'])) {
                $this->addUsingAlias(LoginAttemptTableMap::COL_ATTEMPTED_AT, $attemptedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptTableMap::COL_ATTEMPTED_AT, $attemptedAt, $comparison);
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%', Criteria::LIKE); // WHERE username LIKE '%fooValue%'
     * </code>
     *
     * @param string $username The value to use as filter.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function filterByUsername($username = null, $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptTableMap::COL_USERNAME, $username, $comparison);
    }

    /**
     * Filter the query on the pass column
     *
     * Example usage:
     * <code>
     * $query->filterByPass(1234); // WHERE pass = 1234
     * $query->filterByPass(array(12, 34)); // WHERE pass IN (12, 34)
     * $query->filterByPass(array('min' => 12)); // WHERE pass > 12
     * </code>
     *
     * @param mixed $pass The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function filterByPass($pass = null, $comparison = null)
    {
        if (is_array($pass)) {
            $useMinMax = false;
            if (isset($pass['min'])) {
                $this->addUsingAlias(LoginAttemptTableMap::COL_PASS, $pass['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($pass['max'])) {
                $this->addUsingAlias(LoginAttemptTableMap::COL_PASS, $pass['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        return $this->addUsingAlias(LoginAttemptTableMap::COL_PASS, $pass, $comparison);
    }

    /**
     * Exclude object from result
     *
     * @param ChildLoginAttempt $loginAttempt Object to remove from the list of results
     *
     * @return $this|ChildLoginAttemptQuery The current query, for fluid interface
     */
    public function prune($loginAttempt = null)
    {
        if ($loginAttempt) {
            $this->addUsingAlias(LoginAttemptTableMap::COL_ID, $loginAttempt->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the login_attempts table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LoginAttemptTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            LoginAttemptTableMap::clearInstancePool();
            LoginAttemptTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LoginAttemptTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(LoginAttemptTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            LoginAttemptTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            LoginAttemptTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

} // LoginAttemptQuery
