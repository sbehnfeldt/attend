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
use flapjack\attend\database\GroupPermission as ChildGroupPermission;
use flapjack\attend\database\GroupPermissionQuery as ChildGroupPermissionQuery;
use flapjack\attend\database\Map\GroupPermissionTableMap;

/**
 * Base class that represents a query for the `group_permissions` table.
 *
 * @method     ChildGroupPermissionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildGroupPermissionQuery orderByGroupId($order = Criteria::ASC) Order by the group_id column
 * @method     ChildGroupPermissionQuery orderByPermissionId($order = Criteria::ASC) Order by the permission_id column
 *
 * @method     ChildGroupPermissionQuery groupById() Group by the id column
 * @method     ChildGroupPermissionQuery groupByGroupId() Group by the group_id column
 * @method     ChildGroupPermissionQuery groupByPermissionId() Group by the permission_id column
 *
 * @method     ChildGroupPermissionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildGroupPermissionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildGroupPermissionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildGroupPermissionQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildGroupPermissionQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildGroupPermissionQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildGroupPermissionQuery leftJoinGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the Group relation
 * @method     ChildGroupPermissionQuery rightJoinGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Group relation
 * @method     ChildGroupPermissionQuery innerJoinGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the Group relation
 *
 * @method     ChildGroupPermissionQuery joinWithGroup($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Group relation
 *
 * @method     ChildGroupPermissionQuery leftJoinWithGroup() Adds a LEFT JOIN clause and with to the query using the Group relation
 * @method     ChildGroupPermissionQuery rightJoinWithGroup() Adds a RIGHT JOIN clause and with to the query using the Group relation
 * @method     ChildGroupPermissionQuery innerJoinWithGroup() Adds a INNER JOIN clause and with to the query using the Group relation
 *
 * @method     ChildGroupPermissionQuery leftJoinPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the Permission relation
 * @method     ChildGroupPermissionQuery rightJoinPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Permission relation
 * @method     ChildGroupPermissionQuery innerJoinPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the Permission relation
 *
 * @method     ChildGroupPermissionQuery joinWithPermission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Permission relation
 *
 * @method     ChildGroupPermissionQuery leftJoinWithPermission() Adds a LEFT JOIN clause and with to the query using the Permission relation
 * @method     ChildGroupPermissionQuery rightJoinWithPermission() Adds a RIGHT JOIN clause and with to the query using the Permission relation
 * @method     ChildGroupPermissionQuery innerJoinWithPermission() Adds a INNER JOIN clause and with to the query using the Permission relation
 *
 * @method     \flapjack\attend\database\GroupQuery|\flapjack\attend\database\PermissionQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildGroupPermission|null findOne(?ConnectionInterface $con = null) Return the first ChildGroupPermission matching the query
 * @method     ChildGroupPermission findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildGroupPermission matching the query, or a new ChildGroupPermission object populated from the query conditions when no match is found
 *
 * @method     ChildGroupPermission|null findOneById(int $id) Return the first ChildGroupPermission filtered by the id column
 * @method     ChildGroupPermission|null findOneByGroupId(int $group_id) Return the first ChildGroupPermission filtered by the group_id column
 * @method     ChildGroupPermission|null findOneByPermissionId(int $permission_id) Return the first ChildGroupPermission filtered by the permission_id column
 *
 * @method     ChildGroupPermission requirePk($key, ?ConnectionInterface $con = null) Return the ChildGroupPermission by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildGroupPermission requireOne(?ConnectionInterface $con = null) Return the first ChildGroupPermission matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildGroupPermission requireOneById(int $id) Return the first ChildGroupPermission filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildGroupPermission requireOneByGroupId(int $group_id) Return the first ChildGroupPermission filtered by the group_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildGroupPermission requireOneByPermissionId(int $permission_id) Return the first ChildGroupPermission filtered by the permission_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildGroupPermission[]|Collection find(?ConnectionInterface $con = null) Return ChildGroupPermission objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildGroupPermission> find(?ConnectionInterface $con = null) Return ChildGroupPermission objects based on current ModelCriteria
 *
 * @method     ChildGroupPermission[]|Collection findById(int|array<int> $id) Return ChildGroupPermission objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildGroupPermission> findById(int|array<int> $id) Return ChildGroupPermission objects filtered by the id column
 * @method     ChildGroupPermission[]|Collection findByGroupId(int|array<int> $group_id) Return ChildGroupPermission objects filtered by the group_id column
 * @psalm-method Collection&\Traversable<ChildGroupPermission> findByGroupId(int|array<int> $group_id) Return ChildGroupPermission objects filtered by the group_id column
 * @method     ChildGroupPermission[]|Collection findByPermissionId(int|array<int> $permission_id) Return ChildGroupPermission objects filtered by the permission_id column
 * @psalm-method Collection&\Traversable<ChildGroupPermission> findByPermissionId(int|array<int> $permission_id) Return ChildGroupPermission objects filtered by the permission_id column
 *
 * @method     ChildGroupPermission[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildGroupPermission> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class GroupPermissionQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\GroupPermissionQuery object.
     *
     * @param  string  $dbName  The database name
     * @param  string  $modelName  The phpName of a model, e.g. 'Book'
     * @param  string  $modelAlias  The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\flapjack\\attend\\database\\GroupPermission',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildGroupPermissionQuery object.
     *
     * @param  string  $modelAlias  The alias of a model in the query
     * @param  Criteria  $criteria  Optional Criteria to build the query from
     *
     * @return ChildGroupPermissionQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildGroupPermissionQuery) {
            return $criteria;
        }
        $query = new ChildGroupPermissionQuery();
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
     * @return ChildGroupPermission|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(GroupPermissionTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = GroupPermissionTableMap::getInstanceFromPool(
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
     * @return ChildGroupPermission A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, group_id, permission_id FROM group_permissions WHERE id = :p0';
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
            /** @var ChildGroupPermission $obj */
            $obj = new ChildGroupPermission();
            $obj->hydrate($row);
            GroupPermissionTableMap::addInstanceToPool(
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
     * @return ChildGroupPermission|array|mixed the result, formatted by the current formatter
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

        $this->addUsingAlias(GroupPermissionTableMap::COL_ID, $key, Criteria::EQUAL);

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

        $this->addUsingAlias(GroupPermissionTableMap::COL_ID, $keys, Criteria::IN);

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
                $this->addUsingAlias(GroupPermissionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(GroupPermissionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(GroupPermissionTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the group_id column
     *
     * Example usage:
     * <code>
     * $query->filterByGroupId(1234); // WHERE group_id = 1234
     * $query->filterByGroupId(array(12, 34)); // WHERE group_id IN (12, 34)
     * $query->filterByGroupId(array('min' => 12)); // WHERE group_id > 12
     * </code>
     *
     * @see       filterByGroup()
     *
     * @param  mixed  $groupId  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByGroupId($groupId = null, ?string $comparison = null)
    {
        if (is_array($groupId)) {
            $useMinMax = false;
            if (isset($groupId['min'])) {
                $this->addUsingAlias(GroupPermissionTableMap::COL_GROUP_ID, $groupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($groupId['max'])) {
                $this->addUsingAlias(GroupPermissionTableMap::COL_GROUP_ID, $groupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(GroupPermissionTableMap::COL_GROUP_ID, $groupId, $comparison);

        return $this;
    }

    /**
     * Filter the query on the permission_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPermissionId(1234); // WHERE permission_id = 1234
     * $query->filterByPermissionId(array(12, 34)); // WHERE permission_id IN (12, 34)
     * $query->filterByPermissionId(array('min' => 12)); // WHERE permission_id > 12
     * </code>
     *
     * @see       filterByPermission()
     *
     * @param  mixed  $permissionId  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPermissionId($permissionId = null, ?string $comparison = null)
    {
        if (is_array($permissionId)) {
            $useMinMax = false;
            if (isset($permissionId['min'])) {
                $this->addUsingAlias(
                    GroupPermissionTableMap::COL_PERMISSION_ID,
                    $permissionId['min'],
                    Criteria::GREATER_EQUAL
                );
                $useMinMax = true;
            }
            if (isset($permissionId['max'])) {
                $this->addUsingAlias(
                    GroupPermissionTableMap::COL_PERMISSION_ID,
                    $permissionId['max'],
                    Criteria::LESS_EQUAL
                );
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(GroupPermissionTableMap::COL_PERMISSION_ID, $permissionId, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Group object
     *
     * @param  \flapjack\attend\database\Group|ObjectCollection  $group  The related object(s) to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByGroup($group, ?string $comparison = null)
    {
        if ($group instanceof \flapjack\attend\database\Group) {
            return $this
                ->addUsingAlias(GroupPermissionTableMap::COL_GROUP_ID, $group->getId(), $comparison);
        } elseif ($group instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(
                    GroupPermissionTableMap::COL_GROUP_ID,
                    $group->toKeyValue('PrimaryKey', 'Id'),
                    $comparison
                );

            return $this;
        } else {
            throw new PropelException(
                'filterByGroup() only accepts arguments of type \flapjack\attend\database\Group or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the Group relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinGroup(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Group');

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
            $this->addJoinObject($join, 'Group');
        }

        return $this;
    }

    /**
     * Use the Group relation Group object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\GroupQuery A secondary query class using the current class as primary query
     */
    public function useGroupQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGroup($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Group', '\flapjack\attend\database\GroupQuery');
    }

    /**
     * Use the Group relation Group object
     *
     * @param  callable(\flapjack\attend\database\GroupQuery):\flapjack\attend\database\GroupQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withGroupQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useGroupQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Group table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\GroupQuery The inner query object of the EXISTS statement
     */
    public function useGroupExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\GroupQuery */
        $q = $this->useExistsQuery('Group', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Group table for a NOT EXISTS query.
     *
     * @see useGroupExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\GroupQuery The inner query object of the NOT EXISTS statement
     */
    public function useGroupNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\GroupQuery */
        $q = $this->useExistsQuery('Group', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Group table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\GroupQuery The inner query object of the IN statement
     */
    public function useInGroupQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\GroupQuery */
        $q = $this->useInQuery('Group', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Group table for a NOT IN query.
     *
     * @see useGroupInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\GroupQuery The inner query object of the NOT IN statement
     */
    public function useNotInGroupQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\GroupQuery */
        $q = $this->useInQuery('Group', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Permission object
     *
     * @param  \flapjack\attend\database\Permission|ObjectCollection  $permission  The related object(s) to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPermission($permission, ?string $comparison = null)
    {
        if ($permission instanceof \flapjack\attend\database\Permission) {
            return $this
                ->addUsingAlias(GroupPermissionTableMap::COL_PERMISSION_ID, $permission->getId(), $comparison);
        } elseif ($permission instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(
                    GroupPermissionTableMap::COL_PERMISSION_ID,
                    $permission->toKeyValue('PrimaryKey', 'Id'),
                    $comparison
                );

            return $this;
        } else {
            throw new PropelException(
                'filterByPermission() only accepts arguments of type \flapjack\attend\database\Permission or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the Permission relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinPermission(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Permission');

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
            $this->addJoinObject($join, 'Permission');
        }

        return $this;
    }

    /**
     * Use the Permission relation Permission object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\PermissionQuery A secondary query class using the current class as primary query
     */
    public function usePermissionQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinPermission($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Permission', '\flapjack\attend\database\PermissionQuery');
    }

    /**
     * Use the Permission relation Permission object
     *
     * @param  callable(\flapjack\attend\database\PermissionQuery):\flapjack\attend\database\PermissionQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withPermissionQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->usePermissionQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Permission table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the EXISTS statement
     */
    public function usePermissionExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\PermissionQuery */
        $q = $this->useExistsQuery('Permission', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Permission table for a NOT EXISTS query.
     *
     * @see usePermissionExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the NOT EXISTS statement
     */
    public function usePermissionNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\PermissionQuery */
        $q = $this->useExistsQuery('Permission', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Permission table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the IN statement
     */
    public function useInPermissionQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\PermissionQuery */
        $q = $this->useInQuery('Permission', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Permission table for a NOT IN query.
     *
     * @see usePermissionInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the NOT IN statement
     */
    public function useNotInPermissionQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\PermissionQuery */
        $q = $this->useInQuery('Permission', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param  ChildGroupPermission  $groupPermission  Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($groupPermission = null)
    {
        if ($groupPermission) {
            $this->addUsingAlias(GroupPermissionTableMap::COL_ID, $groupPermission->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the group_permissions table.
     *
     * @param  ConnectionInterface  $con  the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(GroupPermissionTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            GroupPermissionTableMap::clearInstancePool();
            GroupPermissionTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(GroupPermissionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(GroupPermissionTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            GroupPermissionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            GroupPermissionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
