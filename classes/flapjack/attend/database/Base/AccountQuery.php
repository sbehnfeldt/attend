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
use flapjack\attend\database\Account as ChildAccount;
use flapjack\attend\database\AccountQuery as ChildAccountQuery;
use flapjack\attend\database\Map\AccountTableMap;

/**
 * Base class that represents a query for the `accounts` table.
 *
 * @method     ChildAccountQuery orderById($order = Criteria::ASC) Order by the id column
 * @method     ChildAccountQuery orderByUsername($order = Criteria::ASC) Order by the username column
 * @method     ChildAccountQuery orderByPwhash($order = Criteria::ASC) Order by the pwhash column
 * @method     ChildAccountQuery orderByEmail($order = Criteria::ASC) Order by the email column
 * @method     ChildAccountQuery orderByRole($order = Criteria::ASC) Order by the role column
 *
 * @method     ChildAccountQuery groupById() Group by the id column
 * @method     ChildAccountQuery groupByUsername() Group by the username column
 * @method     ChildAccountQuery groupByPwhash() Group by the pwhash column
 * @method     ChildAccountQuery groupByEmail() Group by the email column
 * @method     ChildAccountQuery groupByRole() Group by the role column
 *
 * @method     ChildAccountQuery leftJoin($relation) Adds a LEFT JOIN clause to the query
 * @method     ChildAccountQuery rightJoin($relation) Adds a RIGHT JOIN clause to the query
 * @method     ChildAccountQuery innerJoin($relation) Adds a INNER JOIN clause to the query
 *
 * @method     ChildAccountQuery leftJoinWith($relation) Adds a LEFT JOIN clause and with to the query
 * @method     ChildAccountQuery rightJoinWith($relation) Adds a RIGHT JOIN clause and with to the query
 * @method     ChildAccountQuery innerJoinWith($relation) Adds a INNER JOIN clause and with to the query
 *
 * @method     ChildAccountQuery leftJoinGroupMember($relationAlias = null) Adds a LEFT JOIN clause to the query using the GroupMember relation
 * @method     ChildAccountQuery rightJoinGroupMember($relationAlias = null) Adds a RIGHT JOIN clause to the query using the GroupMember relation
 * @method     ChildAccountQuery innerJoinGroupMember($relationAlias = null) Adds a INNER JOIN clause to the query using the GroupMember relation
 *
 * @method     ChildAccountQuery joinWithGroupMember($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the GroupMember relation
 *
 * @method     ChildAccountQuery leftJoinWithGroupMember() Adds a LEFT JOIN clause and with to the query using the GroupMember relation
 * @method     ChildAccountQuery rightJoinWithGroupMember() Adds a RIGHT JOIN clause and with to the query using the GroupMember relation
 * @method     ChildAccountQuery innerJoinWithGroupMember() Adds a INNER JOIN clause and with to the query using the GroupMember relation
 *
 * @method     ChildAccountQuery leftJoinIndividualPermission($relationAlias = null) Adds a LEFT JOIN clause to the query using the IndividualPermission relation
 * @method     ChildAccountQuery rightJoinIndividualPermission($relationAlias = null) Adds a RIGHT JOIN clause to the query using the IndividualPermission relation
 * @method     ChildAccountQuery innerJoinIndividualPermission($relationAlias = null) Adds a INNER JOIN clause to the query using the IndividualPermission relation
 *
 * @method     ChildAccountQuery joinWithIndividualPermission($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the IndividualPermission relation
 *
 * @method     ChildAccountQuery leftJoinWithIndividualPermission() Adds a LEFT JOIN clause and with to the query using the IndividualPermission relation
 * @method     ChildAccountQuery rightJoinWithIndividualPermission() Adds a RIGHT JOIN clause and with to the query using the IndividualPermission relation
 * @method     ChildAccountQuery innerJoinWithIndividualPermission() Adds a INNER JOIN clause and with to the query using the IndividualPermission relation
 *
 * @method     ChildAccountQuery leftJoinTokenAuth($relationAlias = null) Adds a LEFT JOIN clause to the query using the TokenAuth relation
 * @method     ChildAccountQuery rightJoinTokenAuth($relationAlias = null) Adds a RIGHT JOIN clause to the query using the TokenAuth relation
 * @method     ChildAccountQuery innerJoinTokenAuth($relationAlias = null) Adds a INNER JOIN clause to the query using the TokenAuth relation
 *
 * @method     ChildAccountQuery joinWithTokenAuth($joinType = Criteria::INNER_JOIN) Adds a join clause and with to the query using the TokenAuth relation
 *
 * @method     ChildAccountQuery leftJoinWithTokenAuth() Adds a LEFT JOIN clause and with to the query using the TokenAuth relation
 * @method     ChildAccountQuery rightJoinWithTokenAuth() Adds a RIGHT JOIN clause and with to the query using the TokenAuth relation
 * @method     ChildAccountQuery innerJoinWithTokenAuth() Adds a INNER JOIN clause and with to the query using the TokenAuth relation
 *
 * @method     \flapjack\attend\database\GroupMemberQuery|\flapjack\attend\database\IndividualPermissionQuery|\flapjack\attend\database\TokenAuthQuery endUse() Finalizes a secondary criteria and merges it with its primary Criteria
 *
 * @method     ChildAccount|null findOne(?ConnectionInterface $con = null) Return the first ChildAccount matching the query
 * @method     ChildAccount findOneOrCreate(?ConnectionInterface $con = null) Return the first ChildAccount matching the query, or a new ChildAccount object populated from the query conditions when no match is found
 *
 * @method     ChildAccount|null findOneById(int $id) Return the first ChildAccount filtered by the id column
 * @method     ChildAccount|null findOneByUsername(string $username) Return the first ChildAccount filtered by the username column
 * @method     ChildAccount|null findOneByPwhash(string $pwhash) Return the first ChildAccount filtered by the pwhash column
 * @method     ChildAccount|null findOneByEmail(string $email) Return the first ChildAccount filtered by the email column
 * @method     ChildAccount|null findOneByRole(string $role) Return the first ChildAccount filtered by the role column
 *
 * @method     ChildAccount requirePk($key, ?ConnectionInterface $con = null) Return the ChildAccount by primary key and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAccount requireOne(?ConnectionInterface $con = null) Return the first ChildAccount matching the query and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildAccount requireOneById(int $id) Return the first ChildAccount filtered by the id column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAccount requireOneByUsername(string $username) Return the first ChildAccount filtered by the username column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAccount requireOneByPwhash(string $pwhash) Return the first ChildAccount filtered by the pwhash column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAccount requireOneByEmail(string $email) Return the first ChildAccount filtered by the email column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 * @method     ChildAccount requireOneByRole(string $role) Return the first ChildAccount filtered by the role column and throws \Propel\Runtime\Exception\EntityNotFoundException when not found
 *
 * @method     ChildAccount[]|Collection find(?ConnectionInterface $con = null) Return ChildAccount objects based on current ModelCriteria
 * @psalm-method Collection&\Traversable<ChildAccount> find(?ConnectionInterface $con = null) Return ChildAccount objects based on current ModelCriteria
 *
 * @method     ChildAccount[]|Collection findById(int|array<int> $id) Return ChildAccount objects filtered by the id column
 * @psalm-method Collection&\Traversable<ChildAccount> findById(int|array<int> $id) Return ChildAccount objects filtered by the id column
 * @method     ChildAccount[]|Collection findByUsername(string|array<string> $username) Return ChildAccount objects filtered by the username column
 * @psalm-method Collection&\Traversable<ChildAccount> findByUsername(string|array<string> $username) Return ChildAccount objects filtered by the username column
 * @method     ChildAccount[]|Collection findByPwhash(string|array<string> $pwhash) Return ChildAccount objects filtered by the pwhash column
 * @psalm-method Collection&\Traversable<ChildAccount> findByPwhash(string|array<string> $pwhash) Return ChildAccount objects filtered by the pwhash column
 * @method     ChildAccount[]|Collection findByEmail(string|array<string> $email) Return ChildAccount objects filtered by the email column
 * @psalm-method Collection&\Traversable<ChildAccount> findByEmail(string|array<string> $email) Return ChildAccount objects filtered by the email column
 * @method     ChildAccount[]|Collection findByRole(string|array<string> $role) Return ChildAccount objects filtered by the role column
 * @psalm-method Collection&\Traversable<ChildAccount> findByRole(string|array<string> $role) Return ChildAccount objects filtered by the role column
 *
 * @method     ChildAccount[]|\Propel\Runtime\Util\PropelModelPager paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 * @psalm-method \Propel\Runtime\Util\PropelModelPager&\Traversable<ChildAccount> paginate($page = 1, $maxPerPage = 10, ?ConnectionInterface $con = null) Issue a SELECT query based on the current ModelCriteria and uses a page and a maximum number of results per page to compute an offset and a limit
 */
abstract class AccountQuery extends ModelCriteria
{
    protected $entityNotFoundExceptionClass = '\\Propel\\Runtime\\Exception\\EntityNotFoundException';

    /**
     * Initializes internal state of \flapjack\attend\database\Base\AccountQuery object.
     *
     * @param  string  $dbName  The database name
     * @param  string  $modelName  The phpName of a model, e.g. 'Book'
     * @param  string  $modelAlias  The alias for the model in this query, e.g. 'b'
     */
    public function __construct(
        $dbName = 'attend',
        $modelName = '\\flapjack\\attend\\database\\Account',
        $modelAlias = null
    ) {
        parent::__construct($dbName, $modelName, $modelAlias);
    }

    /**
     * Returns a new ChildAccountQuery object.
     *
     * @param  string  $modelAlias  The alias of a model in the query
     * @param  Criteria  $criteria  Optional Criteria to build the query from
     *
     * @return ChildAccountQuery
     */
    public static function create(?string $modelAlias = null, ?Criteria $criteria = null): Criteria
    {
        if ($criteria instanceof ChildAccountQuery) {
            return $criteria;
        }
        $query = new ChildAccountQuery();
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
     * @return ChildAccount|array|mixed the result, formatted by the current formatter
     */
    public function findPk($key, ?ConnectionInterface $con = null)
    {
        if ($key === null) {
            return null;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(AccountTableMap::DATABASE_NAME);
        }

        $this->basePreSelect($con);

        if (
            $this->formatter || $this->modelAlias || $this->with || $this->select
            || $this->selectColumns || $this->asColumns || $this->selectModifiers
            || $this->map || $this->having || $this->joins
        ) {
            return $this->findPkComplex($key, $con);
        }

        if ((null !== ($obj = AccountTableMap::getInstanceFromPool(
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
     * @return ChildAccount A model object, or null if the key is not found
     * @throws \Propel\Runtime\Exception\PropelException
     *
     */
    protected function findPkSimple($key, ConnectionInterface $con)
    {
        $sql = 'SELECT id, username, pwhash, email, role FROM accounts WHERE id = :p0';
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
            /** @var ChildAccount $obj */
            $obj = new ChildAccount();
            $obj->hydrate($row);
            AccountTableMap::addInstanceToPool(
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
     * @return ChildAccount|array|mixed the result, formatted by the current formatter
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

        $this->addUsingAlias(AccountTableMap::COL_ID, $key, Criteria::EQUAL);

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

        $this->addUsingAlias(AccountTableMap::COL_ID, $keys, Criteria::IN);

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
                $this->addUsingAlias(AccountTableMap::COL_ID, $id['min'], Criteria::GREATER_EQUAL);
                $useMinMax = true;
            }
            if (isset($id['max'])) {
                $this->addUsingAlias(AccountTableMap::COL_ID, $id['max'], Criteria::LESS_EQUAL);
                $useMinMax = true;
            }
            if ($useMinMax) {
                return $this;
            }
            if (null === $comparison) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AccountTableMap::COL_ID, $id, $comparison);

        return $this;
    }

    /**
     * Filter the query on the username column
     *
     * Example usage:
     * <code>
     * $query->filterByUsername('fooValue');   // WHERE username = 'fooValue'
     * $query->filterByUsername('%fooValue%', Criteria::LIKE); // WHERE username LIKE '%fooValue%'
     * $query->filterByUsername(['foo', 'bar']); // WHERE username IN ('foo', 'bar')
     * </code>
     *
     * @param  string|string[]  $username  The value to use as filter.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByUsername($username = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($username)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AccountTableMap::COL_USERNAME, $username, $comparison);

        return $this;
    }

    /**
     * Filter the query on the pwhash column
     *
     * Example usage:
     * <code>
     * $query->filterByPwhash('fooValue');   // WHERE pwhash = 'fooValue'
     * $query->filterByPwhash('%fooValue%', Criteria::LIKE); // WHERE pwhash LIKE '%fooValue%'
     * $query->filterByPwhash(['foo', 'bar']); // WHERE pwhash IN ('foo', 'bar')
     * </code>
     *
     * @param  string|string[]  $pwhash  The value to use as filter.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByPwhash($pwhash = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($pwhash)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AccountTableMap::COL_PWHASH, $pwhash, $comparison);

        return $this;
    }

    /**
     * Filter the query on the email column
     *
     * Example usage:
     * <code>
     * $query->filterByEmail('fooValue');   // WHERE email = 'fooValue'
     * $query->filterByEmail('%fooValue%', Criteria::LIKE); // WHERE email LIKE '%fooValue%'
     * $query->filterByEmail(['foo', 'bar']); // WHERE email IN ('foo', 'bar')
     * </code>
     *
     * @param  string|string[]  $email  The value to use as filter.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByEmail($email = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($email)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AccountTableMap::COL_EMAIL, $email, $comparison);

        return $this;
    }

    /**
     * Filter the query on the role column
     *
     * Example usage:
     * <code>
     * $query->filterByRole('fooValue');   // WHERE role = 'fooValue'
     * $query->filterByRole('%fooValue%', Criteria::LIKE); // WHERE role LIKE '%fooValue%'
     * $query->filterByRole(['foo', 'bar']); // WHERE role IN ('foo', 'bar')
     * </code>
     *
     * @param  string|string[]  $role  The value to use as filter.
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByRole($role = null, ?string $comparison = null)
    {
        if (null === $comparison) {
            if (is_array($role)) {
                $comparison = Criteria::IN;
            }
        }

        $this->addUsingAlias(AccountTableMap::COL_ROLE, $role, $comparison);

        return $this;
    }

    /**
     * Filter the query by a related \flapjack\attend\database\GroupMember object
     *
     * @param  \flapjack\attend\database\GroupMember|ObjectCollection  $groupMember  the related object to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByGroupMember($groupMember, ?string $comparison = null)
    {
        if ($groupMember instanceof \flapjack\attend\database\GroupMember) {
            $this
                ->addUsingAlias(AccountTableMap::COL_ID, $groupMember->getAccountId(), $comparison);

            return $this;
        } elseif ($groupMember instanceof ObjectCollection) {
            $this
                ->useGroupMemberQuery()
                ->filterByPrimaryKeys($groupMember->getPrimaryKeys())
                ->endUse();

            return $this;
        } else {
            throw new PropelException(
                'filterByGroupMember() only accepts arguments of type \flapjack\attend\database\GroupMember or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the GroupMember relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinGroupMember(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('GroupMember');

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
            $this->addJoinObject($join, 'GroupMember');
        }

        return $this;
    }

    /**
     * Use the GroupMember relation GroupMember object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\GroupMemberQuery A secondary query class using the current class as primary query
     */
    public function useGroupMemberQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinGroupMember($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'GroupMember', '\flapjack\attend\database\GroupMemberQuery');
    }

    /**
     * Use the GroupMember relation GroupMember object
     *
     * @param  callable(\flapjack\attend\database\GroupMemberQuery):\flapjack\attend\database\GroupMemberQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withGroupMemberQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useGroupMemberQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to GroupMember table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\GroupMemberQuery The inner query object of the EXISTS statement
     */
    public function useGroupMemberExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\GroupMemberQuery */
        $q = $this->useExistsQuery('GroupMember', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to GroupMember table for a NOT EXISTS query.
     *
     * @see useGroupMemberExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\GroupMemberQuery The inner query object of the NOT EXISTS statement
     */
    public function useGroupMemberNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\GroupMemberQuery */
        $q = $this->useExistsQuery('GroupMember', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to GroupMember table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\GroupMemberQuery The inner query object of the IN statement
     */
    public function useInGroupMemberQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\GroupMemberQuery */
        $q = $this->useInQuery('GroupMember', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to GroupMember table for a NOT IN query.
     *
     * @see useGroupMemberInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\GroupMemberQuery The inner query object of the NOT IN statement
     */
    public function useNotInGroupMemberQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\GroupMemberQuery */
        $q = $this->useInQuery('GroupMember', $modelAlias, $queryClass, 'NOT IN');
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
                ->addUsingAlias(AccountTableMap::COL_ID, $individualPermission->getAccountId(), $comparison);

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
     * Filter the query by a related \flapjack\attend\database\TokenAuth object
     *
     * @param  \flapjack\attend\database\TokenAuth|ObjectCollection  $tokenAuth  the related object to use as filter
     * @param  string|null  $comparison  Operator to use for the column comparison, defaults to Criteria::EQUAL
     *
     * @return $this The current query, for fluid interface
     */
    public function filterByTokenAuth($tokenAuth, ?string $comparison = null)
    {
        if ($tokenAuth instanceof \flapjack\attend\database\TokenAuth) {
            $this
                ->addUsingAlias(AccountTableMap::COL_ID, $tokenAuth->getAccountId(), $comparison);

            return $this;
        } elseif ($tokenAuth instanceof ObjectCollection) {
            $this
                ->useTokenAuthQuery()
                ->filterByPrimaryKeys($tokenAuth->getPrimaryKeys())
                ->endUse();

            return $this;
        } else {
            throw new PropelException(
                'filterByTokenAuth() only accepts arguments of type \flapjack\attend\database\TokenAuth or Collection'
            );
        }
    }

    /**
     * Adds a JOIN clause to the query using the TokenAuth relation
     *
     * @param  string|null  $relationAlias  Optional alias for the relation
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this The current query, for fluid interface
     */
    public function joinTokenAuth(?string $relationAlias = null, ?string $joinType = Criteria::INNER_JOIN)
    {
        $tableMap    = $this->getTableMap();
        $relationMap = $tableMap->getRelation('TokenAuth');

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
            $this->addJoinObject($join, 'TokenAuth');
        }

        return $this;
    }

    /**
     * Use the TokenAuth relation TokenAuth object
     *
     * @see useQuery()
     *
     * @param  string  $relationAlias  optional alias for the relation,
     *                                   to be used as main alias in the secondary query
     * @param  string  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return \flapjack\attend\database\TokenAuthQuery A secondary query class using the current class as primary query
     */
    public function useTokenAuthQuery($relationAlias = null, $joinType = Criteria::INNER_JOIN)
    {
        return $this
            ->joinTokenAuth($relationAlias, $joinType)
            ->useQuery($relationAlias ? $relationAlias : 'TokenAuth', '\flapjack\attend\database\TokenAuthQuery');
    }

    /**
     * Use the TokenAuth relation TokenAuth object
     *
     * @param  callable(\flapjack\attend\database\TokenAuthQuery):\flapjack\attend\database\TokenAuthQuery  $callable  A function working on the related query
     *
     * @param  string|null  $relationAlias  optional alias for the relation
     *
     * @param  string|null  $joinType  Accepted values are null, 'left join', 'right join', 'inner join'
     *
     * @return $this
     */
    public function withTokenAuthQuery(
        callable $callable,
        string $relationAlias = null,
        ?string $joinType = Criteria::INNER_JOIN
    ) {
        $relatedQuery = $this->useTokenAuthQuery(
            $relationAlias,
            $joinType
        );
        $callable($relatedQuery);
        $relatedQuery->endUse();

        return $this;
    }

    /**
     * Use the relation to TokenAuth table for an EXISTS query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     * @param  string  $typeOfExists  Either ExistsQueryCriterion::TYPE_EXISTS or ExistsQueryCriterion::TYPE_NOT_EXISTS
     *
     * @return \flapjack\attend\database\TokenAuthQuery The inner query object of the EXISTS statement
     */
    public function useTokenAuthExistsQuery($modelAlias = null, $queryClass = null, $typeOfExists = 'EXISTS')
    {
        /** @var $q \flapjack\attend\database\TokenAuthQuery */
        $q = $this->useExistsQuery('TokenAuth', $modelAlias, $queryClass, $typeOfExists);
        return $q;
    }

    /**
     * Use the relation to TokenAuth table for a NOT EXISTS query.
     *
     * @see useTokenAuthExistsQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the exists query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\TokenAuthQuery The inner query object of the NOT EXISTS statement
     */
    public function useTokenAuthNotExistsQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\TokenAuthQuery */
        $q = $this->useExistsQuery('TokenAuth', $modelAlias, $queryClass, 'NOT EXISTS');
        return $q;
    }

    /**
     * Use the relation to TokenAuth table for an IN query.
     *
     * @see \Propel\Runtime\ActiveQuery\ModelCriteria::useInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the IN query, like ExtendedBookQuery::class
     * @param  string  $typeOfIn  Criteria::IN or Criteria::NOT_IN
     *
     * @return \flapjack\attend\database\TokenAuthQuery The inner query object of the IN statement
     */
    public function useInTokenAuthQuery($modelAlias = null, $queryClass = null, $typeOfIn = 'IN')
    {
        /** @var $q \flapjack\attend\database\TokenAuthQuery */
        $q = $this->useInQuery('TokenAuth', $modelAlias, $queryClass, $typeOfIn);
        return $q;
    }

    /**
     * Use the relation to TokenAuth table for a NOT IN query.
     *
     * @see useTokenAuthInQuery()
     *
     * @param  string|null  $modelAlias  sets an alias for the nested query
     * @param  string|null  $queryClass  Allows to use a custom query class for the NOT IN query, like ExtendedBookQuery::class
     *
     * @return \flapjack\attend\database\TokenAuthQuery The inner query object of the NOT IN statement
     */
    public function useNotInTokenAuthQuery($modelAlias = null, $queryClass = null)
    {
        /** @var $q \flapjack\attend\database\TokenAuthQuery */
        $q = $this->useInQuery('TokenAuth', $modelAlias, $queryClass, 'NOT IN');
        return $q;
    }

    /**
     * Exclude object from result
     *
     * @param  ChildAccount  $account  Object to remove from the list of results
     *
     * @return $this The current query, for fluid interface
     */
    public function prune($account = null)
    {
        if ($account) {
            $this->addUsingAlias(AccountTableMap::COL_ID, $account->getId(), Criteria::NOT_EQUAL);
        }

        return $this;
    }

    /**
     * Deletes all rows from the accounts table.
     *
     * @param  ConnectionInterface  $con  the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public function doDeleteAll(?ConnectionInterface $con = null): int
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(AccountTableMap::DATABASE_NAME);
        }

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con) {
            $affectedRows = 0; // initialize var to track total num of affected rows
            $affectedRows += parent::doDeleteAll($con);
            // Because this db requires some delete cascade/set null emulation, we have to
            // clear the cached instance *after* the emulation has happened (since
            // instances get re-added by the select statement contained therein).
            AccountTableMap::clearInstancePool();
            AccountTableMap::clearRelatedInstancePool();

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
            $con = Propel::getServiceContainer()->getWriteConnection(AccountTableMap::DATABASE_NAME);
        }

        $criteria = $this;

        // Set the correct dbName
        $criteria->setDbName(AccountTableMap::DATABASE_NAME);

        // use transaction because $criteria could contain info
        // for more than one table or we could emulating ON DELETE CASCADE, etc.
        return $con->transaction(function () use ($con, $criteria) {
            $affectedRows = 0; // initialize var to track total num of affected rows

            AccountTableMap::removeInstanceFromPool($criteria);

            $affectedRows += ModelCriteria::delete($con);
            AccountTableMap::clearRelatedInstancePool();

            return $affectedRows;
        });
    }

}
