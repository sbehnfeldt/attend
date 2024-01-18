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
use flapjack\attend\database\Permission as ChildPermission;
use flapjack\attend\database\PermissionQuery as ChildPermissionQuery;
use flapjack\attend\database\Map\PermissionTableMap;

/**
 * Base class that represents a query for the `permissions` table.
 *
 * @method     ChildPermissionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildPermissionQuery orderBySlug($order = Criteria::ASC) Order by the slug column
 * @method     ChildPermissionQuery orderByDescription($order = Criteria::ASC) Order by the description column
 *
 * @method     ChildPermissionQuery groupById() Group by the id column
 * @method     ChildPermissionQuery groupBySlug() Group by the slug column
 * @method     ChildPermissionQuery groupByDescription() Group by the description column
 *
 * @method     ChildPermissionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildPermissionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildPermissionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildPermissionQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildPermissionQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildPermissionQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildPermissionQuery leftJoinGroupPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the GroupPermission relation
 * @method     ChildPermissionQuery rightJoinGroupPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GroupPermission relation
 * @method     ChildPermissionQuery innerJoinGroupPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the GroupPermission relation
 *
 * @method     ChildPermissionQuery joinWithGroupPermission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the GroupPermission relation
 *
 * @method     ChildPermissionQuery leftJoinWithGroupPermission() Adds a LEFT JOIN clause and with to the query using the GroupPermission relation
 * @method     ChildPermissionQuery rightJoinWithGroupPermission() Adds a RIGHT JOIN clause and with to the query using the GroupPermission relation
 * @method     ChildPermissionQuery innerJoinWithGroupPermission() Adds a INNER JOIN clause and with to the query using the GroupPermission relation
 *
 * @method     ChildPermissionQuery leftJoinIndividualPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the IndividualPermission relation
 * @method     ChildPermissionQuery rightJoinIndividualPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IndividualPermission relation
 * @method     ChildPermissionQuery innerJoinIndividualPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the IndividualPermission relation
 *
 * @method     ChildPermissionQuery joinWithIndividualPermission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IndividualPermission relation
 *
 * @method     ChildPermissionQuery leftJoinWithIndividualPermission() Adds a LEFT JOIN clause and with to the query using the IndividualPermission relation
 * @method     ChildPermissionQuery rightJoinWithIndividualPermission() Adds a RIGHT JOIN clause and with to the query using the IndividualPermission relation
 * @method     ChildPermissionQuery innerJoinWithIndividualPermission() Adds a INNER JOIN clause and with to the query using the IndividualPermission relation
 *
 * @method     \flapjack\attend\database\GroupPermissionQuery|\flapjack\attend\database\IndividualPermissionQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildPermission|null findOne(?ConnectionInterface $con = null) Return the first ChildPermission matching the query
 * @method     ChildPermission findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildPermission matching the query, or a new ChildPermission object populated from the query conditions when no match is found
 *
 * @method     ChildPermission|null findOneById(int $id) Return the first ChildPermission filtered by the id column
 * @method     ChildPermission|null findOneBySlug(string $slug) Return the first ChildPermission filtered by the slug column
 * @method     ChildPermission|null findOneByDescription(string $description) Return the first ChildPermission filtered by the description column
 *
 * @method     ChildPermission requirePk($key, ?ConnectionInterface $con = null) Return the ChildPermission by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPermission requireOne(?ConnectionInterface $con = null) Return the first ChildPermission matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildPermission requireOneById(int $id) Return the first ChildPermission filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPermission requireOneBySlug(string $slug) Return the first ChildPermission filtered by the slug column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildPermission requireOneByDescription(string $description) Return the first ChildPermission filtered by the description column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildPermission[]|Collection find(?ConnectionInterface $con = null) Return ChildPermission objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildPermission> find(?ConnectionInterface $con = null) Return ChildPermission objects based on current ModelCriteria
 *
 * @method     ChildPermission[]|Collection findById(int|array<int> $id) Return ChildPermission objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildPermission> findById(int|array<int> $id) Return ChildPermission objects filtered by the id column
 * @method     ChildPermission[]|Collection findBySlug(string|array<string> $slug) Return ChildPermission objects filtered by the slug column
 * @psalm-method Collection&\Traversable<ChildPermission> findBySlug(string|array<string> $slug) Return ChildPermission objects filtered by the slug column
 * @method     ChildPermission[]|Collection findByDescription(string|array<string> $description) Return ChildPermission objects filtered by the description column
 * @psalm-method Collection&\Traversable<ChildPermission> findByDescription(string|array<string> $description) Return ChildPermission objects filtered by the description column
 *
 * @method     ChildPermission[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildPermission> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class PermissionQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\PermissionQuery object.
     *
     * @param  string  $dbName  The database name
     * @param  string  $modelName  The phpName of a model, e.g. 'Book'
     * @param  string  $modelAlias  The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\flapjack\\attend\\database\\Permission',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildPermissionQuery object.
     *
     * @param  string  $modelAlias  The alias of a model in the query
     * @param  Criteria  $criteria  Optional Criteria to build the query from
     *
     * @return ChildPermissionQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildPermissionQuery) {
            return $criteria;
        }
        $query = new ChildPermissionQuery();
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
     * @param  mixed  $key  Primary key to use for the query
     * @param  ConnectionInterface  $con  an optional connection object
     *
     * @return ChildPermission|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(PermissionTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = PermissionTableMap::getInstanceFromPool(
                null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string)$key : $key
            )))) {
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
     * @return ChildPermission A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, slug, description FROM permissions WHERE id = :p0';
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
            /** @var ChildPermission $obj */
            $obj = new ChildPermission();
            $obj->hydrate($row);
            PermissionTableMap::addInstanceToPool(
                $obj,
                null === $key || is_scalar($key) || is_callable([$key, '__toString']) ? (string)$key : $key
            );
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
     * @return ChildPermission|array|mixed the result, formatted by the current formatter
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

        $this->addUsingAlias(PermissionTableMap::COL_ID, $key, Criteria::EQUAL);

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

        $this->addUsingAlias(PermissionTableMap::COL_ID, $keys, Criteria::IN);

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
                $this->addUsingAlias(PermissionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(PermissionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(PermissionTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the slug column
     *
     * Example usage:
     * <code>
     * $query->filterBySlug('fooValue');   // WHERE slug = 'fooValue'
     * $query->filterBySlug('%fooValue%', Criteria::LIKE); // WHERE slug LIKE '%fooValue%'
     * $query->filterBySlug(['foo', 'bar']); // WHERE slug IN ('foo', 'bar')
     * </code>
     *
     * @param  string|string[]  $slug  The value to use as filter.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterBySlug($slug = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($slug)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(PermissionTableMap::COL_SLUG, $slug, $comparison);

        return $this;
    }

    /**
     * Filter the query on the description column
     *
     * Example usage:
     * <code>
     * $query->filterByDescription('fooValue');   // WHERE description = 'fooValue'
     * $query->filterByDescription('%fooValue%', Criteria::LIKE); // WHERE description LIKE '%fooValue%'
     * $query->filterByDescription(['foo', 'bar']); // WHERE description IN ('foo', 'bar')
     * </code>
     *
     * @param  string|string[]  $description  The value to use as filter.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByDescription($description = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($description)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(PermissionTableMap::COL_DESCRIPTION, $description, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\GroupPermission object
     *
     * @param  \flapjack\attend\database\GroupPermission|ObjectCollection  $groupPermission  the related object to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByGroupPermission($groupPermission, ?string $comparison = null)
    {
        if ($groupPermission instanceof \flapjack\attend\database\GroupPermission) {
            $this
                ->addUsingAlias(PermissionTableMap::COL_ID, $groupPermission->getPermissionId(), $comparison);

            return $this;
        } elseif ($groupPermission instanceof ObjectCollection) {
            $this
                ->useGroupPermissionQuery()
                ->filterByPrimaryKeys($groupPermission->getPrimaryKeys())
                ->endUse();

            return $this;
        } else {
            throw new PropelException(
                'filterByGroupPermission() only accepts arguments of type \flapjack\attend\database\GroupPermission or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the GroupPermission relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinGroupPermission(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GroupPermission');

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
            $this->addJoinObject($join, 'GroupPermission');
        }

        return $this;
    }

    /**
     * Use the GroupPermission relation GroupPermission object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\GroupPermissionQuery A secondary query class using the current class as primary query
     */
    public function useGroupPermissionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGroupPermission($relationAlias, $joinType)
            ->useQuery(
                $relationAlias ? $relationAlias : 'GroupPermission',
                '\flapjack\attend\database\GroupPermissionQuery'
            );
    }

    /**
     * Use the GroupPermission relation GroupPermission object
     *
     * @param  callable(\flapjack\attend\database\GroupPermissionQuery):\flapjack\attend\database\GroupPermissionQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withGroupPermissionQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useGroupPermissionQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to GroupPermission table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\GroupPermissionQuery The inner query object of the EXISTS statement
     */
    public function useGroupPermissionExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\GroupPermissionQuery */
        $q = $this->useExistsQuery('GroupPermission', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to GroupPermission table for a NOT EXISTS query.
     *
     * @see useGroupPermissionExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\GroupPermissionQuery The inner query object of the NOT EXISTS statement
     */
    public function useGroupPermissionNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\GroupPermissionQuery */
        $q = $this->useExistsQuery('GroupPermission', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to GroupPermission table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\GroupPermissionQuery The inner query object of the IN statement
     */
    public function useInGroupPermissionQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\GroupPermissionQuery */
        $q = $this->useInQuery('GroupPermission', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to GroupPermission table for a NOT IN query.
     *
     * @see useGroupPermissionInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\GroupPermissionQuery The inner query object of the NOT IN statement
     */
    public function useNotInGroupPermissionQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\GroupPermissionQuery */
        $q = $this->useInQuery('GroupPermission', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\IndividualPermission object
     *
     * @param  \flapjack\attend\database\IndividualPermission|ObjectCollection  $individualPermission  the related object to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByIndividualPermission($individualPermission, ?string $comparison = null)
    {
        if ($individualPermission instanceof \flapjack\attend\database\IndividualPermission) {
            $this
                ->addUsingAlias(PermissionTableMap::COL_ID, $individualPermission->getPermissionsId(), $comparison);

            return $this;
        } elseif ($individualPermission instanceof ObjectCollection) {
            $this
                ->useIndividualPermissionQuery()
                ->filterByPrimaryKeys($individualPermission->getPrimaryKeys())
                ->endUse();

            return $this;
        } else {
            throw new PropelException(
                'filterByIndividualPermission() only accepts arguments of type \flapjack\attend\database\IndividualPermission or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the IndividualPermission relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinIndividualPermission(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('IndividualPermission');

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
            $this->addJoinObject($join, 'IndividualPermission');
        }

        return $this;
    }

    /**
     * Use the IndividualPermission relation IndividualPermission object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\IndividualPermissionQuery A secondary query class using the current class as primary query
     */
    public function useIndividualPermissionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinIndividualPermission($relationAlias, $joinType)
            ->useQuery(
                $relationAlias ? $relationAlias : 'IndividualPermission',
                '\flapjack\attend\database\IndividualPermissionQuery'
            );
    }

    /**
     * Use the IndividualPermission relation IndividualPermission object
     *
     * @param  callable(\flapjack\attend\database\IndividualPermissionQuery):\flapjack\attend\database\IndividualPermissionQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withIndividualPermissionQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useIndividualPermissionQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to IndividualPermission table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\IndividualPermissionQuery The inner query object of the EXISTS statement
     */
    public function useIndividualPermissionExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\IndividualPermissionQuery */
        $q = $this->useExistsQuery('IndividualPermission', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to IndividualPermission table for a NOT EXISTS query.
     *
     * @see useIndividualPermissionExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\IndividualPermissionQuery The inner query object of the NOT EXISTS statement
     */
    public function useIndividualPermissionNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\IndividualPermissionQuery */
        $q = $this->useExistsQuery('IndividualPermission', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to IndividualPermission table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\IndividualPermissionQuery The inner query object of the IN statement
     */
    public function useInIndividualPermissionQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\IndividualPermissionQuery */
        $q = $this->useInQuery('IndividualPermission', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to IndividualPermission table for a NOT IN query.
     *
     * @see useIndividualPermissionInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\IndividualPermissionQuery The inner query object of the NOT IN statement
     */
    public function useNotInIndividualPermissionQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\IndividualPermissionQuery */
        $q = $this->useInQuery('IndividualPermission', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param  ChildPermission  $permission  Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($permission = null)
    {
        if ($permission) {
            $this->addUsingAlias(PermissionTableMap::COL_ID, $permission->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the permissions table.
     *
     * @param  ConnectionInterface  $con  the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(PermissionTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            PermissionTableMap::clearInstancePool();
            PermissionTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(PermissionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(PermissionTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            PermissionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            PermissionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
