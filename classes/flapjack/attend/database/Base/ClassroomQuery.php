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
use flapjack\attend\database\Classroom as ChildClassroom;
use flapjack\attend\database\ClassroomQuery as ChildClassroomQuery;
use flapjack\attend\database\Map\ClassroomTableMap;

/**
 * Base class that represents a query for the `classrooms` table.
 *
 * @method     ChildClassroomQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildClassroomQuery orderByLabel($order = Criteria::ASC) Order by the label column
 * @method     ChildClassroomQuery orderByOrdering($order = Criteria::ASC) Order by the ordering column
 * @method     ChildClassroomQuery orderByCreatorId($order = Criteria::ASC) Order by the creator_id column
 * @method     ChildClassroomQuery orderByCreatedAt($order = Criteria::ASC) Order by the created_at column
 * @method     ChildClassroomQuery orderByUpdaterId($order = Criteria::ASC) Order by the updater_id column
 * @method     ChildClassroomQuery orderByUpdatedAt($order = Criteria::ASC) Order by the updated_at column
 *
 * @method     ChildClassroomQuery groupById() Group by the id column
 * @method     ChildClassroomQuery groupByLabel() Group by the label column
 * @method     ChildClassroomQuery groupByOrdering() Group by the ordering column
 * @method     ChildClassroomQuery groupByCreatorId() Group by the creator_id column
 * @method     ChildClassroomQuery groupByCreatedAt() Group by the created_at column
 * @method     ChildClassroomQuery groupByUpdaterId() Group by the updater_id column
 * @method     ChildClassroomQuery groupByUpdatedAt() Group by the updated_at column
 *
 * @method     ChildClassroomQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildClassroomQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildClassroomQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildClassroomQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildClassroomQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildClassroomQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildClassroomQuery leftJoinCreatedBy($relationAlias = null) Adds a LEFT JOIN clause to the query using the CreatedBy relation
 * @method     ChildClassroomQuery rightJoinCreatedBy($relationAlias = null) Adds a RIGHT JOIN clause to the query using the CreatedBy relation
 * @method     ChildClassroomQuery innerJoinCreatedBy($relationAlias = null) Adds a INNER JOIN clause to the query using the CreatedBy relation
 *
 * @method     ChildClassroomQuery joinWithCreatedBy($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the CreatedBy relation
 *
 * @method     ChildClassroomQuery leftJoinWithCreatedBy() Adds a LEFT JOIN clause and with to the query using the CreatedBy relation
 * @method     ChildClassroomQuery rightJoinWithCreatedBy() Adds a RIGHT JOIN clause and with to the query using the CreatedBy relation
 * @method     ChildClassroomQuery innerJoinWithCreatedBy() Adds a INNER JOIN clause and with to the query using the CreatedBy relation
 *
 * @method     ChildClassroomQuery leftJoinUpdatedBy($relationAlias = null) Adds a LEFT JOIN clause to the query using the UpdatedBy relation
 * @method     ChildClassroomQuery rightJoinUpdatedBy($relationAlias = null) Adds a RIGHT JOIN clause to the query using the UpdatedBy relation
 * @method     ChildClassroomQuery innerJoinUpdatedBy($relationAlias = null) Adds a INNER JOIN clause to the query using the UpdatedBy relation
 *
 * @method     ChildClassroomQuery joinWithUpdatedBy($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the UpdatedBy relation
 *
 * @method     ChildClassroomQuery leftJoinWithUpdatedBy() Adds a LEFT JOIN clause and with to the query using the UpdatedBy relation
 * @method     ChildClassroomQuery rightJoinWithUpdatedBy() Adds a RIGHT JOIN clause and with to the query using the UpdatedBy relation
 * @method     ChildClassroomQuery innerJoinWithUpdatedBy() Adds a INNER JOIN clause and with to the query using the UpdatedBy relation
 *
 * @method     ChildClassroomQuery leftJoinStudent($relationAlias = null) Adds a LEFT JOIN clause to the query using the Student relation
 * @method     ChildClassroomQuery rightJoinStudent($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Student relation
 * @method     ChildClassroomQuery innerJoinStudent($relationAlias = null) Adds a INNER JOIN clause to the query using the Student relation
 *
 * @method     ChildClassroomQuery joinWithStudent($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Student relation
 *
 * @method     ChildClassroomQuery leftJoinWithStudent() Adds a LEFT JOIN clause and with to the query using the Student relation
 * @method     ChildClassroomQuery rightJoinWithStudent() Adds a RIGHT JOIN clause and with to the query using the Student relation
 * @method     ChildClassroomQuery innerJoinWithStudent() Adds a INNER JOIN clause and with to the query using the Student relation
 *
 * @method     \flapjack\attend\database\AccountQuery|\flapjack\attend\database\AccountQuery|\flapjack\attend\database\StudentQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildClassroom|null findOne(?ConnectionInterface $con = null) Return the first ChildClassroom matching the query
 * @method     ChildClassroom findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildClassroom matching the query, or a new ChildClassroom object populated from the query conditions when no match is found
 *
 * @method     ChildClassroom|null findOneById(int $id) Return the first ChildClassroom filtered by the id column
 * @method     ChildClassroom|null findOneByLabel(string $label) Return the first ChildClassroom filtered by the label column
 * @method     ChildClassroom|null findOneByOrdering(int $ordering) Return the first ChildClassroom filtered by the ordering column
 * @method     ChildClassroom|null findOneByCreatorId(int $creator_id) Return the first ChildClassroom filtered by the creator_id column
 * @method     ChildClassroom|null findOneByCreatedAt(string $created_at) Return the first ChildClassroom filtered by the created_at column
 * @method     ChildClassroom|null findOneByUpdaterId(int $updater_id) Return the first ChildClassroom filtered by the updater_id column
 * @method     ChildClassroom|null findOneByUpdatedAt(string $updated_at) Return the first ChildClassroom filtered by the updated_at column
 *
 * @method     ChildClassroom requirePk($key, ?ConnectionInterface $con = null) Return the ChildClassroom by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOne(?ConnectionInterface $con = null) Return the first ChildClassroom matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildClassroom requireOneById(int $id) Return the first ChildClassroom filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOneByLabel(string $label) Return the first ChildClassroom filtered by the label column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOneByOrdering(int $ordering) Return the first ChildClassroom filtered by the ordering column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOneByCreatorId(int $creator_id) Return the first ChildClassroom filtered by the creator_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOneByCreatedAt(string $created_at) Return the first ChildClassroom filtered by the created_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOneByUpdaterId(int $updater_id) Return the first ChildClassroom filtered by the updater_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildClassroom requireOneByUpdatedAt(string $updated_at) Return the first ChildClassroom filtered by the updated_at column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildClassroom[]|Collection find(?ConnectionInterface $con = null) Return ChildClassroom objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildClassroom> find(?ConnectionInterface $con = null) Return ChildClassroom objects based on current ModelCriteria
 *
 * @method     ChildClassroom[]|Collection findById(int|array<int> $id) Return ChildClassroom objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildClassroom> findById(int|array<int> $id) Return ChildClassroom objects filtered by the id column
 * @method     ChildClassroom[]|Collection findByLabel(string|array<string> $label) Return ChildClassroom objects filtered by the label column
 * @psalm-method Collection&\Traversable<ChildClassroom> findByLabel(string|array<string> $label) Return ChildClassroom objects filtered by the label column
 * @method     ChildClassroom[]|Collection findByOrdering(int|array<int> $ordering) Return ChildClassroom objects filtered by the ordering column
 * @psalm-method Collection&\Traversable<ChildClassroom> findByOrdering(int|array<int> $ordering) Return ChildClassroom objects filtered by the ordering column
 * @method     ChildClassroom[]|Collection findByCreatorId(int|array<int> $creator_id) Return ChildClassroom objects filtered by the creator_id column
 * @psalm-method Collection&\Traversable<ChildClassroom> findByCreatorId(int|array<int> $creator_id) Return ChildClassroom objects filtered by the creator_id column
 * @method     ChildClassroom[]|Collection findByCreatedAt(string|array<string> $created_at) Return ChildClassroom objects filtered by the created_at column
 * @psalm-method Collection&\Traversable<ChildClassroom> findByCreatedAt(string|array<string> $created_at) Return ChildClassroom objects filtered by the created_at column
 * @method     ChildClassroom[]|Collection findByUpdaterId(int|array<int> $updater_id) Return ChildClassroom objects filtered by the updater_id column
 * @psalm-method Collection&\Traversable<ChildClassroom> findByUpdaterId(int|array<int> $updater_id) Return ChildClassroom objects filtered by the updater_id column
 * @method     ChildClassroom[]|Collection findByUpdatedAt(string|array<string> $updated_at) Return ChildClassroom objects filtered by the updated_at column
 * @psalm-method Collection&\Traversable<ChildClassroom> findByUpdatedAt(string|array<string> $updated_at) Return ChildClassroom objects filtered by the updated_at column
 *
 * @method     ChildClassroom[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildClassroom> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class ClassroomQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\ClassroomQuery object.
     *
     * @param string $dbName The database name
     * @param string $modelName The phpName of a model, e.g. 'Book'
     * @param string $modelAlias The alias for the model in this query, e.g. 'b'
     */
    public function __construct($dbName = 'attend', $modelName = '\\flapjack\\attend\\database\\Classroom', $modelAlias = null)
    {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildClassroomQuery object.
     *
     * @param string $modelAlias The alias of a model in the query
     * @param Criteria $criteria Optional Criteria to build the query from
     *
     * @return ChildClassroomQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildClassroomQuery) {
            return $criteria;
        }
        $query = new ChildClassroomQuery();
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
     * @return ChildClassroom|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(ClassroomTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = ClassroomTableMap::getInstanceFromPool(null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key)))) {
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
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return ChildClassroom A model object, or null if the key is not found
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, label, ordering, creator_id, created_at, updater_id, updated_at FROM classrooms WHERE id = :p0';
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
            /** @var ChildClassroom $obj */
            $obj = new ChildClassroom();
            $obj->hydrate($row);
            ClassroomTableMap::addInstanceToPool($obj, null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string) $key : $key);
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
     * @return ChildClassroom|array|mixed the result, formatted by the current formatter
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
     * @return Collection|array|mixed the list of results, formatted by the current formatter
     */
    public function findPks($keys, ?ConnectionInterface $con = null)
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
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKey($key)
    {

        $this->addUsingAlias(ClassroomTableMap::COL_ID, $key, Criteria::EQUAL);

        return $this;
    }

    /**
     * Filter the query by a list of primary keys
     *
     * @param array|int $keys The list of primary key to use for the query
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPrimaryKeys($keys)
    {

        $this->addUsingAlias(ClassroomTableMap::COL_ID, $keys, Criteria::IN);

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
     * @param mixed $id The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterById($id = null, ?string $comparison = null)
    {
        if (is_array($id)) {
            $useMinMax = false;
            if (isset($id['min'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the label column
     *
     * Example usage:
     * <code>
     * $query->filterByLabel('fooValue');   // WHERE label = 'fooValue'
     * $query->filterByLabel('%fooValue%', Criteria::LIKE); // WHERE label LIKE '%fooValue%'
     * $query->filterByLabel(['foo', 'bar']); // WHERE label IN ('foo', 'bar')
     * </code>
     *
     * @param string|string[] $label The value to use as filter.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByLabel($label = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($label)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_LABEL, $label, $comparison);

        return $this;
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
     * @param mixed $ordering The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByOrdering($ordering = null, ?string $comparison = null)
    {
        if (is_array($ordering)) {
            $useMinMax = false;
            if (isset($ordering['min'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_ORDERING, $ordering['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($ordering['max'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_ORDERING, $ordering['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_ORDERING, $ordering, $comparison);

        return $this;
    }

    /**
     * Filter the query on the creator_id column
     *
     * Example usage:
     * <code>
     * $query->filterByCreatorId(1234); // WHERE creator_id = 1234
     * $query->filterByCreatorId(array(12, 34)); // WHERE creator_id IN (12, 34)
     * $query->filterByCreatorId(array('min' => 12)); // WHERE creator_id > 12
     * </code>
     *
     * @see       filterByCreatedBy()
     *
     * @param mixed $creatorId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByCreatorId($creatorId = null, ?string $comparison = null)
    {
        if (is_array($creatorId)) {
            $useMinMax = false;
            if (isset($creatorId['min'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_CREATOR_ID, $creatorId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($creatorId['max'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_CREATOR_ID, $creatorId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_CREATOR_ID, $creatorId, $comparison);

        return $this;
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
     * @param mixed $createdAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByCreatedAt($createdAt = null, ?string $comparison = null)
    {
        if (is_array($createdAt)) {
            $useMinMax = false;
            if (isset($createdAt['min'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_CREATED_AT, $createdAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($createdAt['max'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_CREATED_AT, $createdAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_CREATED_AT, $createdAt, $comparison);

        return $this;
    }

    /**
     * Filter the query on the updater_id column
     *
     * Example usage:
     * <code>
     * $query->filterByUpdaterId(1234); // WHERE updater_id = 1234
     * $query->filterByUpdaterId(array(12, 34)); // WHERE updater_id IN (12, 34)
     * $query->filterByUpdaterId(array('min' => 12)); // WHERE updater_id > 12
     * </code>
     *
     * @see       filterByUpdatedBy()
     *
     * @param mixed $updaterId The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByUpdaterId($updaterId = null, ?string $comparison = null)
    {
        if (is_array($updaterId)) {
            $useMinMax = false;
            if (isset($updaterId['min'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_UPDATER_ID, $updaterId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updaterId['max'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_UPDATER_ID, $updaterId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_UPDATER_ID, $updaterId, $comparison);

        return $this;
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
     * @param mixed $updatedAt The value to use as filter.
     *              Values can be integers (unix timestamps), DateTime objects, or strings.
     *              Empty strings are treated as NULL.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByUpdatedAt($updatedAt = null, ?string $comparison = null)
    {
        if (is_array($updatedAt)) {
            $useMinMax = false;
            if (isset($updatedAt['min'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_UPDATED_AT, $updatedAt['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($updatedAt['max'])) {
                $this->addUsingAlias(ClassroomTableMap::COL_UPDATED_AT, $updatedAt['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(ClassroomTableMap::COL_UPDATED_AT, $updatedAt, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Account object
     *
     * @param \flapjack\attend\database\Account|ObjectCollection $account The related object(s) to use as filter
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByCreatedBy($account, ?string $comparison = null)
    {
        if ($account instanceof \flapjack\attend\database\Account) {
            return $this
                ->addUsingAlias(ClassroomTableMap::COL_CREATOR_ID, $account->getId(), $comparison);
        } elseif ($account instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(ClassroomTableMap::COL_CREATOR_ID, $account->toKeyValue('PrimaryKey', 'Id'), $comparison);

            return $this;
        } else {
            throw new PropelException('filterByCreatedBy() only accepts arguments of type \flapjack\attend\database\Account or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the CreatedBy relation
     *
     * @param string|null $relationAlias Optional alias for the relation
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinCreatedBy(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('CreatedBy');

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
            $this->addJoinObject($join, 'CreatedBy');
        }

        return $this;
    }

    /**
     * Use the CreatedBy relation Account object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\AccountQuery A secondary query class using the current class as primary query
     */
    public function useCreatedByQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinCreatedBy($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'CreatedBy', '\flapjack\attend\database\AccountQuery');
    }

    /**
     * Use the CreatedBy relation Account object
     *
     * @param callable(\flapjack\attend\database\AccountQuery):\flapjack\attend\database\AccountQuery $callable A function working on the related query
     *
     * @param string|null $relationAlias optional alias for the relation
     *
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withCreatedByQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useCreatedByQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the CreatedBy relation to the Account table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param string $typeOfExists Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the EXISTS statement
     */
    public function useCreatedByExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useExistsQuery('CreatedBy', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the CreatedBy relation to the Account table for a NOT EXISTS query.
     *
     * @see useCreatedByExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT EXISTS statement
     */
    public function useCreatedByNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useExistsQuery('CreatedBy', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the CreatedBy relation to the Account table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param string $typeOfIn Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the IN statement
     */
    public function useInCreatedByQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('CreatedBy', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the CreatedBy relation to the Account table for a NOT IN query.
     *
     * @see useCreatedByInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT IN statement
     */
    public function useNotInCreatedByQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('CreatedBy', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Account object
     *
     * @param \flapjack\attend\database\Account|ObjectCollection $account The related object(s) to use as filter
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByUpdatedBy($account, ?string $comparison = null)
    {
        if ($account instanceof \flapjack\attend\database\Account) {
            return $this
                ->addUsingAlias(ClassroomTableMap::COL_UPDATER_ID, $account->getId(), $comparison);
        } elseif ($account instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(ClassroomTableMap::COL_UPDATER_ID, $account->toKeyValue('PrimaryKey', 'Id'), $comparison);

            return $this;
        } else {
            throw new PropelException('filterByUpdatedBy() only accepts arguments of type \flapjack\attend\database\Account or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the UpdatedBy relation
     *
     * @param string|null $relationAlias Optional alias for the relation
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinUpdatedBy(?string $relationAlias = null, ?string $joinType = Criteria::LEFT_JOIN)
    {
        $tableMap = $this->getTableMap();
        $relationMap = $tableMap->getRelation('UpdatedBy');

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
            $this->addJoinObject($join, 'UpdatedBy');
        }

        return $this;
    }

    /**
     * Use the UpdatedBy relation Account object
     *
     * @see useQuery()
     *
     * @param string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\AccountQuery A secondary query class using the current class as primary query
     */
    public function useUpdatedByQuery($relationAlias = null, $joinType = Criteria::LEFT_JOIN)
    {
        return $this
            ->joinUpdatedBy($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'UpdatedBy', '\flapjack\attend\database\AccountQuery');
    }

    /**
     * Use the UpdatedBy relation Account object
     *
     * @param callable(\flapjack\attend\database\AccountQuery):\flapjack\attend\database\AccountQuery $callable A function working on the related query
     *
     * @param string|null $relationAlias optional alias for the relation
     *
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withUpdatedByQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::LEFT_JOIN
    ) {
        $relatedQuery = $this->useUpdatedByQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the UpdatedBy relation to the Account table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param string $typeOfExists Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the EXISTS statement
     */
    public function useUpdatedByExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useExistsQuery('UpdatedBy', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the UpdatedBy relation to the Account table for a NOT EXISTS query.
     *
     * @see useUpdatedByExistsQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT EXISTS statement
     */
    public function useUpdatedByNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useExistsQuery('UpdatedBy', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the UpdatedBy relation to the Account table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param string $typeOfIn Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the IN statement
     */
    public function useInUpdatedByQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('UpdatedBy', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the UpdatedBy relation to the Account table for a NOT IN query.
     *
     * @see useUpdatedByInQuery()
     *
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT IN statement
     */
    public function useNotInUpdatedByQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('UpdatedBy', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Student object
     *
     * @param \flapjack\attend\database\Student|ObjectCollection $student the related object to use as filter
     * @param string|null $comparison Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByStudent($student, ?string $comparison = null)
    {
        if ($student instanceof \flapjack\attend\database\Student) {
            $this
                ->addUsingAlias(ClassroomTableMap::COL_ID, $student->getClassroomId(), $comparison);

            return $this;
        } elseif ($student instanceof ObjectCollection) {
            $this
                ->useStudentQuery()
                ->filterByPrimaryKeys($student->getPrimaryKeys())
                ->endUse();

            return $this;
        } else {
            throw new PropelException('filterByStudent() only accepts arguments of type \flapjack\attend\database\Student or Collection');
        }
    }

    /**
     * Adds a JOIN clause to the query using the Student relation
     *
     * @param string|null $relationAlias Optional alias for the relation
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinStudent(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap = $this->getTableMap();
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
     * @param string $relationAlias optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param string $joinType Accepted values are null, 'left join', 'right join', 'inner join'
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
     * @param callable(\flapjack\attend\database\StudentQuery):\flapjack\attend\database\StudentQuery $callable A function working on the related query
     *
     * @param string|null $relationAlias optional alias for the relation
     *
     * @param string|null $joinType Accepted values are null, 'left join', 'right join', 'inner join'
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
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param string $typeOfExists Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
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
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
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
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param string $typeOfIn Criteria::IN or Criteria::NOT_IN
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
     * @param string|null $modelAlias sets an alias for the nested query
     * @param string|null $queryClass Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
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
     * @param ChildClassroom $classroom Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($classroom = null)
    {
        if ($classroom) {
            $this->addUsingAlias(ClassroomTableMap::COL_ID, $classroom->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the classrooms table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClassroomTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            ClassroomTableMap::clearInstancePool();
            ClassroomTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(ClassroomTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(ClassroomTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            ClassroomTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            ClassroomTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
