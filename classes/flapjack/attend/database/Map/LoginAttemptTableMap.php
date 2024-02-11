<?php

namespace flapjack\attend\database\Map;

use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;
use flapjack\attend\database\LoginAttempt;
use flapjack\attend\database\LoginAttemptQuery;


/**
 * This class defines the structure of the 'login_attempts' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 */
class LoginAttemptTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    public const CLASS_NAME = '.Map.LoginAttemptTableMap';

    /**
     * The default database name for this class
     */
    public const DATABASE_NAME = 'attend';

    /**
     * The table name for this class
     */
    public const TABLE_NAME = 'login_attempts';

    /**
     * The PHP name of this class (PascalCase)
     */
    public const TABLE_PHP_NAME = 'LoginAttempt';

    /**
     * The related Propel class for this table
     */
    public const OM_CLASS = '\\flapjack\\attend\\database\\LoginAttempt';

    /**
     * A class that can be returned by this tableMap
     */
    public const CLASS_DEFAULT = 'LoginAttempt';

    /**
     * The total number of columns
     */
    public const NUM_COLUMNS = 6;

    /**
     * The number of lazy-loaded columns
     */
    public const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    public const NUM_HYDRATE_COLUMNS = 6;

    /**
     * the column name for the id field
     */
    public const COL_ID = 'login_attempts.id';

    /**
     * the column name for the attempted_at field
     */
    public const COL_ATTEMPTED_AT = 'login_attempts.attempted_at';

    /**
     * the column name for the username field
     */
    public const COL_USERNAME = 'login_attempts.username';

    /**
     * the column name for the pass field
     */
    public const COL_PASS = 'login_attempts.pass';

    /**
     * the column name for the note field
     */
    public const COL_NOTE = 'login_attempts.note';

    /**
     * the column name for the logged_out_at field
     */
    public const COL_LOGGED_OUT_AT = 'login_attempts.logged_out_at';

    /**
     * The default string format for model objects of the related table
     */
    public const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     *
     * @var array<string, mixed>
     */
    protected static $fieldNames = [
        self::TYPE_PHPNAME       => ['Id', 'AttemptedAt', 'Username', 'Pass', 'Note', 'LoggedOutAt', ],
        self::TYPE_CAMELNAME     => ['id', 'attemptedAt', 'username', 'pass', 'note', 'loggedOutAt', ],
        self::TYPE_COLNAME       => [LoginAttemptTableMap::COL_ID, LoginAttemptTableMap::COL_ATTEMPTED_AT, LoginAttemptTableMap::COL_USERNAME, LoginAttemptTableMap::COL_PASS, LoginAttemptTableMap::COL_NOTE, LoginAttemptTableMap::COL_LOGGED_OUT_AT, ],
        self::TYPE_FIELDNAME     => ['id', 'attempted_at', 'username', 'pass', 'note', 'logged_out_at', ],
        self::TYPE_NUM           => [0, 1, 2, 3, 4, 5, ]
    ];

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     *
     * @var array<string, mixed>
     */
    protected static $fieldKeys = [
        self::TYPE_PHPNAME       => ['Id' => 0, 'AttemptedAt' => 1, 'Username' => 2, 'Pass' => 3, 'Note' => 4, 'LoggedOutAt' => 5, ],
        self::TYPE_CAMELNAME     => ['id' => 0, 'attemptedAt' => 1, 'username' => 2, 'pass' => 3, 'note' => 4, 'loggedOutAt' => 5, ],
        self::TYPE_COLNAME       => [LoginAttemptTableMap::COL_ID => 0, LoginAttemptTableMap::COL_ATTEMPTED_AT => 1, LoginAttemptTableMap::COL_USERNAME => 2, LoginAttemptTableMap::COL_PASS => 3, LoginAttemptTableMap::COL_NOTE => 4, LoginAttemptTableMap::COL_LOGGED_OUT_AT => 5, ],
        self::TYPE_FIELDNAME     => ['id' => 0, 'attempted_at' => 1, 'username' => 2, 'pass' => 3, 'note' => 4, 'logged_out_at' => 5, ],
        self::TYPE_NUM           => [0, 1, 2, 3, 4, 5, ]
    ];

    /**
     * Holds a list of column names and their normalized version.
     *
     * @var array<string>
     */
    protected $normalizedColumnNameMap = [
        'Id' => 'ID',
        'LoginAttempt.Id' => 'ID',
        'id' => 'ID',
        'loginAttempt.id' => 'ID',
        'LoginAttemptTableMap::COL_ID' => 'ID',
        'COL_ID' => 'ID',
        'login_attempts.id' => 'ID',
        'AttemptedAt' => 'ATTEMPTED_AT',
        'LoginAttempt.AttemptedAt' => 'ATTEMPTED_AT',
        'attemptedAt' => 'ATTEMPTED_AT',
        'loginAttempt.attemptedAt' => 'ATTEMPTED_AT',
        'LoginAttemptTableMap::COL_ATTEMPTED_AT' => 'ATTEMPTED_AT',
        'COL_ATTEMPTED_AT' => 'ATTEMPTED_AT',
        'attempted_at' => 'ATTEMPTED_AT',
        'login_attempts.attempted_at' => 'ATTEMPTED_AT',
        'Username' => 'USERNAME',
        'LoginAttempt.Username' => 'USERNAME',
        'username' => 'USERNAME',
        'loginAttempt.username' => 'USERNAME',
        'LoginAttemptTableMap::COL_USERNAME' => 'USERNAME',
        'COL_USERNAME' => 'USERNAME',
        'login_attempts.username' => 'USERNAME',
        'Pass' => 'PASS',
        'LoginAttempt.Pass' => 'PASS',
        'pass' => 'PASS',
        'loginAttempt.pass' => 'PASS',
        'LoginAttemptTableMap::COL_PASS' => 'PASS',
        'COL_PASS' => 'PASS',
        'login_attempts.pass' => 'PASS',
        'Note' => 'NOTE',
        'LoginAttempt.Note' => 'NOTE',
        'note' => 'NOTE',
        'loginAttempt.note' => 'NOTE',
        'LoginAttemptTableMap::COL_NOTE' => 'NOTE',
        'COL_NOTE' => 'NOTE',
        'login_attempts.note' => 'NOTE',
        'LoggedOutAt' => 'LOGGED_OUT_AT',
        'LoginAttempt.LoggedOutAt' => 'LOGGED_OUT_AT',
        'loggedOutAt' => 'LOGGED_OUT_AT',
        'loginAttempt.loggedOutAt' => 'LOGGED_OUT_AT',
        'LoginAttemptTableMap::COL_LOGGED_OUT_AT' => 'LOGGED_OUT_AT',
        'COL_LOGGED_OUT_AT' => 'LOGGED_OUT_AT',
        'logged_out_at' => 'LOGGED_OUT_AT',
        'login_attempts.logged_out_at' => 'LOGGED_OUT_AT',
    ];

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function initialize(): void
    {
        // attributes
        $this->setName('login_attempts');
        $this->setPhpName('LoginAttempt');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\flapjack\\attend\\database\\LoginAttempt');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, null, null);
        $this->addColumn('attempted_at', 'AttemptedAt', 'BIGINT', true, null, null);
        $this->addColumn('username', 'Username', 'VARCHAR', true, 63, null);
        $this->addColumn('pass', 'Pass', 'BOOLEAN', true, 1, null);
        $this->addColumn('note', 'Note', 'VARCHAR', true, 255, null);
        $this->addColumn('logged_out_at', 'LoggedOutAt', 'BIGINT', false, null, null);
    }

    /**
     * Build the RelationMap objects for this table relationships
     *
     * @return void
     */
    public function buildRelations(): void
    {
    }

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array $row Resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string|null The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM): ?string
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] === null) {
            return null;
        }

        return null === $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] || is_scalar($row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)]) || is_callable([$row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)], '__toString']) ? (string) $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)] : $row[TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array $row Resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM)
    {
        return (int) $row[
            $indexType == TableMap::TYPE_NUM
                ? 0 + $offset
                : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)
        ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param bool $withPrefix Whether to return the path with the class name
     * @return string path.to.ClassName
     */
    public static function getOMClass(bool $withPrefix = true): string
    {
        return $withPrefix ? LoginAttemptTableMap::CLASS_DEFAULT : LoginAttemptTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array $row Row returned by DataFetcher->fetch().
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                 One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array (LoginAttempt object, last column rank)
     */
    public static function populateObject(array $row, int $offset = 0, string $indexType = TableMap::TYPE_NUM): array
    {
        $key = LoginAttemptTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = LoginAttemptTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + LoginAttemptTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = LoginAttemptTableMap::OM_CLASS;
            /** @var LoginAttempt $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            LoginAttemptTableMap::addInstanceToPool($obj, $key);
        }

        return [$obj, $col];
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     * @return array<object>
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher): array
    {
        $results = [];

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = LoginAttemptTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = LoginAttemptTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var LoginAttempt $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                LoginAttemptTableMap::addInstanceToPool($obj, $key);
            } // if key exists
        }

        return $results;
    }
    /**
     * Add all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be added to the select list and only loaded
     * on demand.
     *
     * @param Criteria $criteria Object containing the columns to add.
     * @param string|null $alias Optional table alias
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return void
     */
    public static function addSelectColumns(Criteria $criteria, ?string $alias = null): void
    {
        if (null === $alias) {
            $criteria->addSelectColumn(LoginAttemptTableMap::COL_ID);
            $criteria->addSelectColumn(LoginAttemptTableMap::COL_ATTEMPTED_AT);
            $criteria->addSelectColumn(LoginAttemptTableMap::COL_USERNAME);
            $criteria->addSelectColumn(LoginAttemptTableMap::COL_PASS);
            $criteria->addSelectColumn(LoginAttemptTableMap::COL_NOTE);
            $criteria->addSelectColumn(LoginAttemptTableMap::COL_LOGGED_OUT_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.attempted_at');
            $criteria->addSelectColumn($alias . '.username');
            $criteria->addSelectColumn($alias . '.pass');
            $criteria->addSelectColumn($alias . '.note');
            $criteria->addSelectColumn($alias . '.logged_out_at');
        }
    }

    /**
     * Remove all the columns needed to create a new object.
     *
     * Note: any columns that were marked with lazyLoad="true" in the
     * XML schema will not be removed as they are only loaded on demand.
     *
     * @param Criteria $criteria Object containing the columns to remove.
     * @param string|null $alias Optional table alias
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return void
     */
    public static function removeSelectColumns(Criteria $criteria, ?string $alias = null): void
    {
        if (null === $alias) {
            $criteria->removeSelectColumn(LoginAttemptTableMap::COL_ID);
            $criteria->removeSelectColumn(LoginAttemptTableMap::COL_ATTEMPTED_AT);
            $criteria->removeSelectColumn(LoginAttemptTableMap::COL_USERNAME);
            $criteria->removeSelectColumn(LoginAttemptTableMap::COL_PASS);
            $criteria->removeSelectColumn(LoginAttemptTableMap::COL_NOTE);
            $criteria->removeSelectColumn(LoginAttemptTableMap::COL_LOGGED_OUT_AT);
        } else {
            $criteria->removeSelectColumn($alias . '.id');
            $criteria->removeSelectColumn($alias . '.attempted_at');
            $criteria->removeSelectColumn($alias . '.username');
            $criteria->removeSelectColumn($alias . '.pass');
            $criteria->removeSelectColumn($alias . '.note');
            $criteria->removeSelectColumn($alias . '.logged_out_at');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap(): TableMap
    {
        return Propel::getServiceContainer()->getDatabaseMap(LoginAttemptTableMap::DATABASE_NAME)->getTable(LoginAttemptTableMap::TABLE_NAME);
    }

    /**
     * Performs a DELETE on the database, given a LoginAttempt or Criteria object OR a primary key value.
     *
     * @param mixed $values Criteria or LoginAttempt object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
     public static function doDelete($values, ?ConnectionInterface $con = null): int
     {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LoginAttemptTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \flapjack\attend\database\LoginAttempt) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(LoginAttemptTableMap::DATABASE_NAME);
            $criteria->add(LoginAttemptTableMap::COL_ID, (array) $values, Criteria::IN);
        }

        $query = LoginAttemptQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            LoginAttemptTableMap::clearInstancePool();
        } elseif (!is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array) $values as $singleval) {
                LoginAttemptTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the login_attempts table.
     *
     * @param ConnectionInterface $con the connection to use
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(?ConnectionInterface $con = null): int
    {
        return LoginAttemptQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a LoginAttempt or Criteria object.
     *
     * @param mixed $criteria Criteria or LoginAttempt object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     * @return mixed The new primary key.
     * @throws \Propel\Runtime\Exception\PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ?ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(LoginAttemptTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from LoginAttempt object
        }

        if ($criteria->containsKey(LoginAttemptTableMap::COL_ID) && $criteria->keyContainsValue(LoginAttemptTableMap::COL_ID) ) {
            throw new PropelException('Cannot insert a value for auto-increment primary key ('.LoginAttemptTableMap::COL_ID.')');
        }


        // Set the correct dbName
        $query = LoginAttemptQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

}
