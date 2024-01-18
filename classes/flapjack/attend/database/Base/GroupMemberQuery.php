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
use flapjack\attend\database\GroupMember as ChildGroupMember;
use flapjack\attend\database\GroupMemberQuery as ChildGroupMemberQuery;
use flapjack\attend\database\Map\GroupMemberTableMap;

/**
 * Base class that represents a query for the `group_members` table.
 *
 * @method     ChildGroupMemberQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildGroupMemberQuery orderByGroupId($order = Criteria::ASC) Order by the group_id column
 * @method     ChildGroupMemberQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 *
 * @method     ChildGroupMemberQuery groupById() Group by the id column
 * @method     ChildGroupMemberQuery groupByGroupId() Group by the group_id column
 * @method     ChildGroupMemberQuery groupByAccountId() Group by the account_id column
 *
 * @method     ChildGroupMemberQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildGroupMemberQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildGroupMemberQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildGroupMemberQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildGroupMemberQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildGroupMemberQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildGroupMemberQuery leftJoinGroup($relationAlias = null) Adds a LEFT JOIN clause to the query using the Group relation
 * @method     ChildGroupMemberQuery rightJoinGroup($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Group relation
 * @method     ChildGroupMemberQuery innerJoinGroup($relationAlias = null) Adds a INNER JOIN clause to the query using the Group relation
 *
 * @method     ChildGroupMemberQuery joinWithGroup($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Group relation
 *
 * @method     ChildGroupMemberQuery leftJoinWithGroup() Adds a LEFT JOIN clause and with to the query using the Group relation
 * @method     ChildGroupMemberQuery rightJoinWithGroup() Adds a RIGHT JOIN clause and with to the query using the Group relation
 * @method     ChildGroupMemberQuery innerJoinWithGroup() Adds a INNER JOIN clause and with to the query using the Group relation
 *
 * @method     ChildGroupMemberQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method     ChildGroupMemberQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method     ChildGroupMemberQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method     ChildGroupMemberQuery joinWithAccount($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Account relation
 *
 * @method     ChildGroupMemberQuery leftJoinWithAccount() Adds a LEFT JOIN clause and with to the query using the Account relation
 * @method     ChildGroupMemberQuery rightJoinWithAccount() Adds a RIGHT JOIN clause and with to the query using the Account relation
 * @method     ChildGroupMemberQuery innerJoinWithAccount() Adds a INNER JOIN clause and with to the query using the Account relation
 *
 * @method     \flapjack\attend\database\GroupQuery|\flapjack\attend\database\AccountQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildGroupMember|null findOne(?ConnectionInterface $con = null) Return the first ChildGroupMember matching the query
 * @method     ChildGroupMember findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildGroupMember matching the query, or a new ChildGroupMember object populated from the query conditions when no match is found
 *
 * @method     ChildGroupMember|null findOneById(int $id) Return the first ChildGroupMember filtered by the id column
 * @method     ChildGroupMember|null findOneByGroupId(int $group_id) Return the first ChildGroupMember filtered by the group_id column
 * @method     ChildGroupMember|null findOneByAccountId(int $account_id) Return the first ChildGroupMember filtered by the account_id column
 *
 * @method     ChildGroupMember requirePk($key, ?ConnectionInterface $con = null) Return the ChildGroupMember by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildGroupMember requireOne(?ConnectionInterface $con = null) Return the first ChildGroupMember matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildGroupMember requireOneById(int $id) Return the first ChildGroupMember filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildGroupMember requireOneByGroupId(int $group_id) Return the first ChildGroupMember filtered by the group_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildGroupMember requireOneByAccountId(int $account_id) Return the first ChildGroupMember filtered by the account_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildGroupMember[]|Collection find(?ConnectionInterface $con = null) Return ChildGroupMember objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildGroupMember> find(?ConnectionInterface $con = null) Return ChildGroupMember objects based on current ModelCriteria
 *
 * @method     ChildGroupMember[]|Collection findById(int|array<int> $id) Return ChildGroupMember objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildGroupMember> findById(int|array<int> $id) Return ChildGroupMember objects filtered by the id column
 * @method     ChildGroupMember[]|Collection findByGroupId(int|array<int> $group_id) Return ChildGroupMember objects filtered by the group_id column
 * @psalm-method Collection&\Traversable<ChildGroupMember> findByGroupId(int|array<int> $group_id) Return ChildGroupMember objects filtered by the group_id column
 * @method     ChildGroupMember[]|Collection findByAccountId(int|array<int> $account_id) Return ChildGroupMember objects filtered by the account_id column
 * @psalm-method Collection&\Traversable<ChildGroupMember> findByAccountId(int|array<int> $account_id) Return ChildGroupMember objects filtered by the account_id column
 *
 * @method     ChildGroupMember[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildGroupMember> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class GroupMemberQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\GroupMemberQuery object.
     *
     * @param  string  $dbName  The database name
     * @param  string  $modelName  The phpName of a model, e.g. 'Book'
     * @param  string  $modelAlias  The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\flapjack\\attend\\database\\GroupMember',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildGroupMemberQuery object.
     *
     * @param  string  $modelAlias  The alias of a model in the query
     * @param  Criteria  $criteria  Optional Criteria to build the query from
     *
     * @return ChildGroupMemberQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildGroupMemberQuery) {
            return $criteria;
        }
        $query = new ChildGroupMemberQuery();
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
     * @return ChildGroupMember|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(GroupMemberTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = GroupMemberTableMap::getInstanceFromPool(
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
     * @return ChildGroupMember A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, group_id, account_id FROM group_members WHERE id = :p0';
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
            /** @var ChildGroupMember $obj */
            $obj = new ChildGroupMember();
            $obj->hydrate($row);
            GroupMemberTableMap::addInstanceToPool(
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
     * @return ChildGroupMember|array|mixed the result, formatted by the current formatter
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

        $this->addUsingAlias(GroupMemberTableMap::COL_ID, $key, Criteria::EQUAL);

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

        $this->addUsingAlias(GroupMemberTableMap::COL_ID, $keys, Criteria::IN);

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
                $this->addUsingAlias(GroupMemberTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(GroupMemberTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(GroupMemberTableMap::COL_ID, $id, $comparison);

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
                $this->addUsingAlias(GroupMemberTableMap::COL_GROUP_ID, $groupId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($groupId['max'])) {
                $this->addUsingAlias(GroupMemberTableMap::COL_GROUP_ID, $groupId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(GroupMemberTableMap::COL_GROUP_ID, $groupId, $comparison);

        return $this;
    }

    /**
     * Filter the query on the account_id column
     *
     * Example usage:
     * <code>
     * $query->filterByAccountId(1234); // WHERE account_id = 1234
     * $query->filterByAccountId(array(12, 34)); // WHERE account_id IN (12, 34)
     * $query->filterByAccountId(array('min' => 12)); // WHERE account_id > 12
     * </code>
     *
     * @see       filterByAccount()
     *
     * @param  mixed  $accountId  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByAccountId($accountId = null, ?string $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(GroupMemberTableMap::COL_ACCOUNT_ID, $accountId['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(GroupMemberTableMap::COL_ACCOUNT_ID, $accountId['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(GroupMemberTableMap::COL_ACCOUNT_ID, $accountId, $comparison);

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
                ->addUsingAlias(GroupMemberTableMap::COL_GROUP_ID, $group->getId(), $comparison);
        } elseif ($group instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(GroupMemberTableMap::COL_GROUP_ID, $group->toKeyValue('PrimaryKey', 'Id'), $comparison);

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
     * Filter the query by a related \flapjack\attend\database\Account object
     *
     * @param  \flapjack\attend\database\Account|ObjectCollection  $account  The related object(s) to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @throws \Propel\Runtime\Exception\PropelException
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByAccount($account, ?string $comparison = null)
    {
        if ($account instanceof \flapjack\attend\database\Account) {
            return $this
                ->addUsingAlias(GroupMemberTableMap::COL_ACCOUNT_ID, $account->getId(), $comparison);
        } elseif ($account instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(
                    GroupMemberTableMap::COL_ACCOUNT_ID,
                    $account->toKeyValue('PrimaryKey', 'Id'),
                    $comparison
                );

            return $this;
        } else {
            throw new PropelException(
                'filterByAccount() only accepts arguments of type \flapjack\attend\database\Account or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the Account relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinAccount(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('Account');

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
            $this->addJoinObject($join, 'Account');
        }

        return $this;
    }

    /**
     * Use the Account relation Account object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\AccountQuery A secondary query class using the current class as primary query
     */
    public function useAccountQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinAccount($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'Account', '\flapjack\attend\database\AccountQuery');
    }

    /**
     * Use the Account relation Account object
     *
     * @param  callable(\flapjack\attend\database\AccountQuery):\flapjack\attend\database\AccountQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withAccountQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useAccountQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to Account table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the EXISTS statement
     */
    public function useAccountExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useExistsQuery('Account', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to Account table for a NOT EXISTS query.
     *
     * @see useAccountExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT EXISTS statement
     */
    public function useAccountNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useExistsQuery('Account', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to Account table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the IN statement
     */
    public function useInAccountQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('Account', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to Account table for a NOT IN query.
     *
     * @see useAccountInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT IN statement
     */
    public function useNotInAccountQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('Account', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param  ChildGroupMember  $groupMember  Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($groupMember = null)
    {
        if ($groupMember) {
            $this->addUsingAlias(GroupMemberTableMap::COL_ID, $groupMember->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the group_members table.
     *
     * @param  ConnectionInterface  $con  the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(GroupMemberTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            GroupMemberTableMap::clearInstancePool();
            GroupMemberTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(GroupMemberTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(GroupMemberTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            GroupMemberTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            GroupMemberTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
