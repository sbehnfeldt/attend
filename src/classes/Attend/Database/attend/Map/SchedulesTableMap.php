<?php

namespace Attend\Database\attend\Map;

use Attend\Database\attend\Schedules;
use Attend\Database\attend\SchedulesQuery;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\InstancePoolTrait;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\DataFetcher\DataFetcherInterface;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\RelationMap;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Map\TableMapTrait;


/**
 * This class defines the structure of the 'schedules' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 */
class SchedulesTableMap extends TableMap
{
    use InstancePoolTrait;
    use TableMapTrait;

    /**
     * The (dot-path) name of this class
     */
    const CLASS_NAME = '.Map.SchedulesTableMap';

    /**
     * The default database name for this class
     */
    const DATABASE_NAME = 'attend';

    /**
     * The table name for this class
     */
    const TABLE_NAME = 'schedules';

    /**
     * The related Propel class for this table
     */
    const OM_CLASS = '\\Attend\\Database\\attend\\Schedules';

    /**
     * A class that can be returned by this tableMap
     */
    const CLASS_DEFAULT = 'Schedules';

    /**
     * The total number of columns
     */
    const NUM_COLUMNS = 5;

    /**
     * The number of lazy-loaded columns
     */
    const NUM_LAZY_LOAD_COLUMNS = 0;

    /**
     * The number of columns to hydrate (NUM_COLUMNS - NUM_LAZY_LOAD_COLUMNS)
     */
    const NUM_HYDRATE_COLUMNS = 5;

    /**
     * the column name for the id field
     */
    const COL_ID = 'schedules.id';

    /**
     * the column name for the student_id field
     */
    const COL_STUDENT_ID = 'schedules.student_id';

    /**
     * the column name for the schedule field
     */
    const COL_SCHEDULE = 'schedules.schedule';

    /**
     * the column name for the start_date field
     */
    const COL_START_DATE = 'schedules.start_date';

    /**
     * the column name for the entered_at field
     */
    const COL_ENTERED_AT = 'schedules.entered_at';

    /**
     * The default string format for model objects of the related table
     */
    const DEFAULT_STRING_FORMAT = 'YAML';

    /**
     * holds an array of fieldnames
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldNames[self::TYPE_PHPNAME][0] = 'Id'
     */
    protected static $fieldNames = array(
        self::TYPE_PHPNAME   => array('Id', 'StudentId', 'Schedule', 'StartDate', 'EnteredAt',),
        self::TYPE_CAMELNAME => array('id', 'studentId', 'schedule', 'startDate', 'enteredAt',),
        self::TYPE_COLNAME   => array(
            SchedulesTableMap::COL_ID,
            SchedulesTableMap::COL_STUDENT_ID,
            SchedulesTableMap::COL_SCHEDULE,
            SchedulesTableMap::COL_START_DATE,
            SchedulesTableMap::COL_ENTERED_AT,
        ),
        self::TYPE_FIELDNAME => array('id', 'student_id', 'schedule', 'start_date', 'entered_at',),
        self::TYPE_NUM       => array(0, 1, 2, 3, 4,)
    );

    /**
     * holds an array of keys for quick access to the fieldnames array
     *
     * first dimension keys are the type constants
     * e.g. self::$fieldKeys[self::TYPE_PHPNAME]['Id'] = 0
     */
    protected static $fieldKeys = array(
        self::TYPE_PHPNAME   => array(
            'Id'        => 0,
            'StudentId' => 1,
            'Schedule'  => 2,
            'StartDate' => 3,
            'EnteredAt' => 4,
        ),
        self::TYPE_CAMELNAME => array(
            'id'        => 0,
            'studentId' => 1,
            'schedule'  => 2,
            'startDate' => 3,
            'enteredAt' => 4,
        ),
        self::TYPE_COLNAME   => array(
            SchedulesTableMap::COL_ID         => 0,
            SchedulesTableMap::COL_STUDENT_ID => 1,
            SchedulesTableMap::COL_SCHEDULE   => 2,
            SchedulesTableMap::COL_START_DATE => 3,
            SchedulesTableMap::COL_ENTERED_AT => 4,
        ),
        self::TYPE_FIELDNAME => array(
            'id'         => 0,
            'student_id' => 1,
            'schedule'   => 2,
            'start_date' => 3,
            'entered_at' => 4,
        ),
        self::TYPE_NUM       => array(0, 1, 2, 3, 4,)
    );

    /**
     * Initialize the table attributes and columns
     * Relations are not initialized by this method since they are lazy loaded
     *
     * @return void
     * @throws PropelException
     */
    public function initialize()
    {
        // attributes
        $this->setName('schedules');
        $this->setPhpName('Schedules');
        $this->setIdentifierQuoting(false);
        $this->setClassName('\\Attend\\Database\\attend\\Schedules');
        $this->setPackage('');
        $this->setUseIdGenerator(true);
        // columns
        $this->addPrimaryKey('id', 'Id', 'INTEGER', true, 10, null);
        $this->addForeignKey('student_id', 'StudentId', 'INTEGER', 'students', 'id', true, 10, null);
        $this->addColumn('schedule', 'Schedule', 'INTEGER', true, null, 0);
        $this->addColumn('start_date', 'StartDate', 'DATE', true, null, null);
        $this->addColumn('entered_at', 'EnteredAt', 'INTEGER', true, null, null);
    } // initialize()

    /**
     * Build the RelationMap objects for this table relationships
     */
    public function buildRelations()
    {
        $this->addRelation('Students', '\\Attend\\Database\\attend\\Students', RelationMap::MANY_TO_ONE, array(
            0 =>
                array(
                    0 => ':student_id',
                    1 => ':id',
                ),
        ), 'CASCADE', null, null, false);
    } // buildRelations()

    /**
     * Retrieves a string version of the primary key from the DB resultset row that can be used to uniquely identify a row in this table.
     *
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, a serialize()d version of the primary key will be returned.
     *
     * @param array $row resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return string The primary key hash of the row
     */
    public static function getPrimaryKeyHashFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        // If the PK cannot be derived from the row, return NULL.
        if ($row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
                TableMap::TYPE_PHPNAME, $indexType) ] === null
        ) {
            return null;
        }

        return null === $row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME,
            $indexType) ] || is_scalar($row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME, $indexType) ]) || is_callable([
            $row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
                TableMap::TYPE_PHPNAME, $indexType) ],
            '__toString'
        ]) ? (string)$row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME,
            $indexType) ] : $row[ TableMap::TYPE_NUM == $indexType ? 0 + $offset : static::translateFieldName('Id',
            TableMap::TYPE_PHPNAME, $indexType) ];
    }

    /**
     * Retrieves the primary key from the DB resultset row
     * For tables with a single-column primary key, that simple pkey value will be returned.  For tables with
     * a multi-column primary key, an array of the primary key columns will be returned.
     *
     * @param array $row resultset row.
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM
     *
     * @return mixed The primary key of the row
     */
    public static function getPrimaryKeyFromRow($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        return (int)$row[ $indexType == TableMap::TYPE_NUM
            ? 0 + $offset
            : self::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType) ];
    }

    /**
     * The class that the tableMap will make instances of.
     *
     * If $withPrefix is true, the returned path
     * uses a dot-path notation which is translated into a path
     * relative to a location on the PHP include_path.
     * (e.g. path.to.MyClass -> 'path/to/MyClass.php')
     *
     * @param boolean $withPrefix Whether or not to return the path with the class name
     *
     * @return string path.to.ClassName
     */
    public static function getOMClass($withPrefix = true)
    {
        return $withPrefix ? SchedulesTableMap::CLASS_DEFAULT : SchedulesTableMap::OM_CLASS;
    }

    /**
     * Populates an object of the default type or an object that inherit from the default.
     *
     * @param array $row row returned by DataFetcher->fetch().
     * @param int $offset The 0-based offset for reading from the resultset row.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
     * One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                           TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     * @return array           (Schedules object, last column rank)
     */
    public static function populateObject($row, $offset = 0, $indexType = TableMap::TYPE_NUM)
    {
        $key = SchedulesTableMap::getPrimaryKeyHashFromRow($row, $offset, $indexType);
        if (null !== ($obj = SchedulesTableMap::getInstanceFromPool($key))) {
            // We no longer rehydrate the object, since this can cause data loss.
            // See http://www.propelorm.org/ticket/509
            // $obj->hydrate($row, $offset, true); // rehydrate
            $col = $offset + SchedulesTableMap::NUM_HYDRATE_COLUMNS;
        } else {
            $cls = SchedulesTableMap::OM_CLASS;
            /** @var Schedules $obj */
            $obj = new $cls();
            $col = $obj->hydrate($row, $offset, false, $indexType);
            SchedulesTableMap::addInstanceToPool($obj, $key);
        }

        return array($obj, $col);
    }

    /**
     * The returned array will contain objects of the default type or
     * objects that inherit from the default.
     *
     * @param DataFetcherInterface $dataFetcher
     *
     * @return array
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function populateObjects(DataFetcherInterface $dataFetcher)
    {
        $results = array();

        // set the class once to avoid overhead in the loop
        $cls = static::getOMClass(false);
        // populate the object(s)
        while ($row = $dataFetcher->fetch()) {
            $key = SchedulesTableMap::getPrimaryKeyHashFromRow($row, 0, $dataFetcher->getIndexType());
            if (null !== ($obj = SchedulesTableMap::getInstanceFromPool($key))) {
                // We no longer rehydrate the object, since this can cause data loss.
                // See http://www.propelorm.org/ticket/509
                // $obj->hydrate($row, 0, true); // rehydrate
                $results[] = $obj;
            } else {
                /** @var Schedules $obj */
                $obj = new $cls();
                $obj->hydrate($row);
                $results[] = $obj;
                SchedulesTableMap::addInstanceToPool($obj, $key);
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
     * @param Criteria $criteria object containing the columns to add.
     * @param string $alias optional table alias
     *
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function addSelectColumns(Criteria $criteria, $alias = null)
    {
        if (null === $alias) {
            $criteria->addSelectColumn(SchedulesTableMap::COL_ID);
            $criteria->addSelectColumn(SchedulesTableMap::COL_STUDENT_ID);
            $criteria->addSelectColumn(SchedulesTableMap::COL_SCHEDULE);
            $criteria->addSelectColumn(SchedulesTableMap::COL_START_DATE);
            $criteria->addSelectColumn(SchedulesTableMap::COL_ENTERED_AT);
        } else {
            $criteria->addSelectColumn($alias . '.id');
            $criteria->addSelectColumn($alias . '.student_id');
            $criteria->addSelectColumn($alias . '.schedule');
            $criteria->addSelectColumn($alias . '.start_date');
            $criteria->addSelectColumn($alias . '.entered_at');
        }
    }

    /**
     * Returns the TableMap related to this object.
     * This method is not needed for general use but a specific application could have a need.
     * @return TableMap
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function getTableMap()
    {
        return Propel::getServiceContainer()->getDatabaseMap(SchedulesTableMap::DATABASE_NAME)->getTable(SchedulesTableMap::TABLE_NAME);
    }

    /**
     * Add a TableMap instance to the database for this tableMap class.
     */
    public static function buildTableMap()
    {
        $dbMap = Propel::getServiceContainer()->getDatabaseMap(SchedulesTableMap::DATABASE_NAME);
        if ( ! $dbMap->hasTable(SchedulesTableMap::TABLE_NAME)) {
            $dbMap->addTableObject(new SchedulesTableMap());
        }
    }

    /**
     * Performs a DELETE on the database, given a Schedules or Criteria object OR a primary key value.
     *
     * @param mixed $values Criteria or Schedules object or primary key or array of primary keys
     *              which is used to create the DELETE statement
     * @param  ConnectionInterface $con the connection to use
     *
     * @return int             The number of affected rows (if supported by underlying database driver).  This includes CASCADE-related rows
     *                         if supported by native driver or if emulated using Propel.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doDelete($values, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SchedulesTableMap::DATABASE_NAME);
        }

        if ($values instanceof Criteria) {
            // rename for clarity
            $criteria = $values;
        } elseif ($values instanceof \Attend\Database\attend\Schedules) { // it's a model object
            // create criteria based on pk values
            $criteria = $values->buildPkeyCriteria();
        } else { // it's a primary key, or an array of pks
            $criteria = new Criteria(SchedulesTableMap::DATABASE_NAME);
            $criteria->add(SchedulesTableMap::COL_ID, (array)$values, Criteria::IN);
        }

        $query = SchedulesQuery::create()->mergeWith($criteria);

        if ($values instanceof Criteria) {
            SchedulesTableMap::clearInstancePool();
        } elseif ( ! is_object($values)) { // it's a primary key, or an array of pks
            foreach ((array)$values as $singleval) {
                SchedulesTableMap::removeInstanceFromPool($singleval);
            }
        }

        return $query->delete($con);
    }

    /**
     * Deletes all rows from the schedules table.
     *
     * @param ConnectionInterface $con the connection to use
     *
     * @return int The number of affected rows (if supported by underlying database driver).
     */
    public static function doDeleteAll(ConnectionInterface $con = null)
    {
        return SchedulesQuery::create()->doDeleteAll($con);
    }

    /**
     * Performs an INSERT on the database, given a Schedules or Criteria object.
     *
     * @param mixed $criteria Criteria or Schedules object containing data that is used to create the INSERT statement.
     * @param ConnectionInterface $con the ConnectionInterface connection to use
     *
     * @return mixed           The new primary key.
     * @throws PropelException Any exceptions caught during processing will be
     *                         rethrown wrapped into a PropelException.
     */
    public static function doInsert($criteria, ConnectionInterface $con = null)
    {
        if (null === $con) {
            $con = Propel::getServiceContainer()->getWriteConnection(SchedulesTableMap::DATABASE_NAME);
        }

        if ($criteria instanceof Criteria) {
            $criteria = clone $criteria; // rename for clarity
        } else {
            $criteria = $criteria->buildCriteria(); // build Criteria from Schedules object
        }

        if ($criteria->containsKey(SchedulesTableMap::COL_ID) && $criteria->keyContainsValue(SchedulesTableMap::COL_ID)) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . SchedulesTableMap::COL_ID . ')');
        }


        // Set the correct dbName
        $query = SchedulesQuery::create()->mergeWith($criteria);

        // use transaction because $criteria could contain info
        // for more than one table (I guess, conceivably)
        return $con->transaction(function () use ($con, $query) {
            return $query->doInsert($con);
        });
    }

} // SchedulesTableMap
// This is the static code needed to register the TableMap for this table with the main Propel class.
//
SchedulesTableMap::buildTableMap();
