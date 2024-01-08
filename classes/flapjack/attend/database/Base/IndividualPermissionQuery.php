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
use flapjack\attend\database\IndividualPermission as ChildIndividualPermission;
use flapjack\attend\database\IndividualPermissionQuery as ChildIndividualPermissionQuery;
use flapjack\attend\database\Map\IndividualPermissionTableMap;

/**
 * Base class that represents a query for the `individual_permissions` table.
 *
 * @method     ChildIndividualPermissionQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildIndividualPermissionQuery orderByAccountId($order = Criteria::ASC) Order by the account_id column
 * @method     ChildIndividualPermissionQuery orderByPermissionsId($order = Criteria::ASC) Order by the permissions_id column
 *
 * @method     ChildIndividualPermissionQuery groupById() Group by the id column
 * @method     ChildIndividualPermissionQuery groupByAccountId() Group by the account_id column
 * @method     ChildIndividualPermissionQuery groupByPermissionsId() Group by the permissions_id column
 *
 * @method     ChildIndividualPermissionQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildIndividualPermissionQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildIndividualPermissionQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildIndividualPermissionQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildIndividualPermissionQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildIndividualPermissionQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildIndividualPermissionQuery leftJoinAccount($relationAlias = null) Adds a LEFT JOIN clause to the query using the Account relation
 * @method     ChildIndividualPermissionQuery rightJoinAccount($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Account relation
 * @method     ChildIndividualPermissionQuery innerJoinAccount($relationAlias = null) Adds a INNER JOIN clause to the query using the Account relation
 *
 * @method     ChildIndividualPermissionQuery joinWithAccount($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Account relation
 *
 * @method     ChildIndividualPermissionQuery leftJoinWithAccount() Adds a LEFT JOIN clause and with to the query using the Account relation
 * @method     ChildIndividualPermissionQuery rightJoinWithAccount() Adds a RIGHT JOIN clause and with to the query using the Account relation
 * @method     ChildIndividualPermissionQuery innerJoinWithAccount() Adds a INNER JOIN clause and with to the query using the Account relation
 *
 * @method     ChildIndividualPermissionQuery leftJoinPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the Permission relation
 * @method     ChildIndividualPermissionQuery rightJoinPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the Permission relation
 * @method     ChildIndividualPermissionQuery innerJoinPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the Permission relation
 *
 * @method     ChildIndividualPermissionQuery joinWithPermission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the Permission relation
 *
 * @method     ChildIndividualPermissionQuery leftJoinWithPermission() Adds a LEFT JOIN clause and with to the query using the Permission relation
 * @method     ChildIndividualPermissionQuery rightJoinWithPermission() Adds a RIGHT JOIN clause and with to the query using the Permission relation
 * @method     ChildIndividualPermissionQuery innerJoinWithPermission() Adds a INNER JOIN clause and with to the query using the Permission relation
 *
 * @method     \flapjack\attend\database\AccountQuery|\flapjack\attend\database\PermissionQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildIndividualPermission|null findOne(?ConnectionInterface $con = null) Return the first ChildIndividualPermission matching the query
 * @method     ChildIndividualPermission findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildIndividualPermission matching the query, or a new ChildIndividualPermission object populated from the query conditions when no match is found
 *
 * @method     ChildIndividualPermission|null findOneById(int $id) Return the first ChildIndividualPermission filtered by the id column
 * @method     ChildIndividualPermission|null findOneByAccountId(int $account_id) Return the first ChildIndividualPermission filtered by the account_id column
 * @method     ChildIndividualPermission|null findOneByPermissionsId(int $permissions_id) Return the first ChildIndividualPermission filtered by the permissions_id column
 *
 * @method     ChildIndividualPermission requirePk($key, ?ConnectionInterface $con = null) Return the ChildIndividualPermission by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIndividualPermission requireOne(?ConnectionInterface $con = null) Return the first ChildIndividualPermission matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildIndividualPermission requireOneById(int $id) Return the first ChildIndividualPermission filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIndividualPermission requireOneByAccountId(int $account_id) Return the first ChildIndividualPermission filtered by the account_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildIndividualPermission requireOneByPermissionsId(int $permissions_id) Return the first ChildIndividualPermission filtered by the permissions_id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildIndividualPermission[]|Collection find(?ConnectionInterface $con = null) Return ChildIndividualPermission objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildIndividualPermission> find(?ConnectionInterface $con = null) Return ChildIndividualPermission objects based on current ModelCriteria
 *
 * @method     ChildIndividualPermission[]|Collection findById(int|array<int> $id) Return ChildIndividualPermission objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildIndividualPermission> findById(int|array<int> $id) Return ChildIndividualPermission objects filtered by the id column
 * @method     ChildIndividualPermission[]|Collection findByAccountId(int|array<int> $account_id) Return ChildIndividualPermission objects filtered by the account_id column
 * @psalm-method Collection&\Traversable<ChildIndividualPermission> findByAccountId(int|array<int> $account_id) Return ChildIndividualPermission objects filtered by the account_id column
 * @method     ChildIndividualPermission[]|Collection findByPermissionsId(int|array<int> $permissions_id) Return ChildIndividualPermission objects filtered by the permissions_id column
 * @psalm-method Collection&\Traversable<ChildIndividualPermission> findByPermissionsId(int|array<int> $permissions_id) Return ChildIndividualPermission objects filtered by the permissions_id column
 *
 * @method     ChildIndividualPermission[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildIndividualPermission> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class IndividualPermissionQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\IndividualPermissionQuery object.
     *
     * @param  string  $dbName  The database name
     * @param  string  $modelName  The phpName of a model, e.g. 'Book'
     * @param  string  $modelAlias  The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\flapjack\\attend\\database\\IndividualPermission',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildIndividualPermissionQuery object.
     *
     * @param  string  $modelAlias  The alias of a model in the query
     * @param  Criteria  $criteria  Optional Criteria to build the query from
     *
     * @return ChildIndividualPermissionQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildIndividualPermissionQuery) {
            return $criteria;
        }
        $query = new ChildIndividualPermissionQuery();
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
     * @return ChildIndividualPermission|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(IndividualPermissionTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = IndividualPermissionTableMap::getInstanceFromPool(
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
     * @return ChildIndividualPermission A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, account_id, permissions_id FROM individual_permissions WHERE id = :p0';
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
            /** @var ChildIndividualPermission $obj */
            $obj = new ChildIndividualPermission();
            $obj->hydrate($row);
            IndividualPermissionTableMap::addInstanceToPool(
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
     * @return ChildIndividualPermission|array|mixed the result, formatted by the current formatter
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
        $this->addUsingAlias(IndividualPermissionTableMap::COL_ID, $key, Criteria::EQUAL);

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
        $this->addUsingAlias(IndividualPermissionTableMap::COL_ID, $keys, Criteria::IN);

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
                $this->addUsingAlias(IndividualPermissionTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(IndividualPermissionTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(IndividualPermissionTableMap::COL_ID, $id, $comparison);

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
     * @param  mixed  $accountId  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     * @see       filterByAccount()
     *
     */
    public function filterByAccountId($accountId = null, ?string $comparison = null)
    {
        if (is_array($accountId)) {
            $useMinMax = false;
            if (isset($accountId['min'])) {
                $this->addUsingAlias(
                    IndividualPermissionTableMap::COL_ACCOUNT_ID,
                    $accountId['min'],
                    Criteria::GREATER_EQUAL
                );
                $useMinMax = true;
            }
            if (isset($accountId['max'])) {
                $this->addUsingAlias(
                    IndividualPermissionTableMap::COL_ACCOUNT_ID,
                    $accountId['max'],
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

        $this->addUsingAlias(IndividualPermissionTableMap::COL_ACCOUNT_ID, $accountId, $comparison);

        return $this;
    }

    /**
     * Filter the query on the permissions_id column
     *
     * Example usage:
     * <code>
     * $query->filterByPermissionsId(1234); // WHERE permissions_id = 1234
     * $query->filterByPermissionsId(array(12, 34)); // WHERE permissions_id IN (12, 34)
     * $query->filterByPermissionsId(array('min' => 12)); // WHERE permissions_id > 12
     * </code>
     *
     * @param  mixed  $permissionsId  The value to use as filter.
     *              Use scalar values for equality.
     *              Use array values for in_array() equivalent.
     *              Use associative array('min' => $minValue, 'max' => $maxValue) for intervals.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     * @see       filterByPermission()
     *
     */
    public function filterByPermissionsId($permissionsId = null, ?string $comparison = null)
    {
        if (is_array($permissionsId)) {
            $useMinMax = false;
            if (isset($permissionsId['min'])) {
                $this->addUsingAlias(
                    IndividualPermissionTableMap::COL_PERMISSIONS_ID,
                    $permissionsId['min'],
                    Criteria::GREATER_EQUAL
                );
                $useMinMax = true;
            }
            if (isset($permissionsId['max'])) {
                $this->addUsingAlias(
                    IndividualPermissionTableMap::COL_PERMISSIONS_ID,
                    $permissionsId['max'],
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

        $this->addUsingAlias(IndividualPermissionTableMap::COL_PERMISSIONS_ID, $permissionsId, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Account object
     *
     * @param  \flapjack\attend\database\Account|ObjectCollection  $account  The related object(s) to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    public function filterByAccount($account, ?string $comparison = null)
    {
        if ($account instanceof \flapjack\attend\database\Account) {
            return $this
                ->addUsingAlias(IndividualPermissionTableMap::COL_ACCOUNT_ID, $account->getId(), $comparison);
        } elseif ($account instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(
                    IndividualPermissionTableMap::COL_ACCOUNT_ID,
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
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\AccountQuery A secondary query class using the current class as primary query
     * @see useQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the EXISTS statement
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT EXISTS statement
     * @see useAccountExistsQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the IN statement
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\AccountQuery The inner query object of the NOT IN statement
     * @see useAccountInQuery()
     *
     */
    public function useNotInAccountQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\AccountQuery */
        $q = $this->useInQuery('Account', $modelAlias, $queryClass, 'NOT IN');

        return $q;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\Permission object
     *
     * @param  \flapjack\attend\database\Permission|ObjectCollection  $permission  The related object(s) to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    public function filterByPermission($permission, ?string $comparison = null)
    {
        if ($permission instanceof \flapjack\attend\database\Permission) {
            return $this
                ->addUsingAlias(IndividualPermissionTableMap::COL_PERMISSIONS_ID, $permission->getId(), $comparison);
        } elseif ($permission instanceof ObjectCollection) {
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }

            $this
                ->addUsingAlias(
                    IndividualPermissionTableMap::COL_PERMISSIONS_ID,
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
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\PermissionQuery A secondary query class using the current class as primary query
     * @see useQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the EXISTS statement
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the NOT EXISTS statement
     * @see usePermissionExistsQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the IN statement
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
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
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\PermissionQuery The inner query object of the NOT IN statement
     * @see usePermissionInQuery()
     *
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
     * @param  ChildIndividualPermission  $individualPermission  Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($individualPermission = null)
    {
        if ($individualPermission) {
            $this->addUsingAlias(
                IndividualPermissionTableMap::COL_ID,
                $individualPermission->getId(),
                Criteria::NOT_EQUAL
            );
        }

        return $this;
    }

    /**
     * Deletes all rows from the individual_permissions table.
     *
     * @param  ConnectionInterface  $con  the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(IndividualPermissionTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            IndividualPermissionTableMap::clearInstancePool();
            IndividualPermissionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

    /**
     * Performs a DELETE on the database based on the current ModelCriteria
     *
     * @param  ConnectionInterface  $con  the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public function delete(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(IndividualPermissionTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(IndividualPermissionTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            IndividualPermissionTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            IndividualPermissionTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
