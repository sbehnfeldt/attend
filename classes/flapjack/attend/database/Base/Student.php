<?php

namespace flapjack\attend\database\Base;

use \Exception;
use \PDO;
use Propel\Runtime\Propel;
use Propel\Runtime\ActiveQuery\Criteria;
use Propel\Runtime\ActiveQuery\ModelCriteria;
use Propel\Runtime\ActiveRecord\ActiveRecordInterface;
use Propel\Runtime\Collection\Collection;
use Propel\Runtime\Collection\ObjectCollection;
use Propel\Runtime\Connection\ConnectionInterface;
use Propel\Runtime\Exception\BadMethodCallException;
use Propel\Runtime\Exception\LogicException;
use Propel\Runtime\Exception\PropelException;
use Propel\Runtime\Map\TableMap;
use Propel\Runtime\Parser\AbstractParser;
use flapjack\attend\database\Attendance as ChildAttendance;
use flapjack\attend\database\AttendanceQuery as ChildAttendanceQuery;
use flapjack\attend\database\Classroom as ChildClassroom;
use flapjack\attend\database\ClassroomQuery as ChildClassroomQuery;
use flapjack\attend\database\Schedule as ChildSchedule;
use flapjack\attend\database\ScheduleQuery as ChildScheduleQuery;
use flapjack\attend\database\Student as ChildStudent;
use flapjack\attend\database\StudentQuery as ChildStudentQuery;
use flapjack\attend\database\Map\AttendanceTableMap;
use flapjack\attend\database\Map\ScheduleTableMap;
use flapjack\attend\database\Map\StudentTableMap;

/**
 * Base class that represents a row from the 'students' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Student implements ActiveRecordInterface
{
    /**
     * TableMap class name
     *
     * @var string
     */
    public const TABLE_MAP = '\\flapjack\\attend\\database\\Map\\StudentTableMap';


    /**
     * attribute to determine if this object has previously been saved.
     * @var bool
     */
    protected $new = true;

    /**
     * attribute to determine whether this object has been deleted.
     * @var bool
     */
    protected $deleted = false;

    /**
     * The columns that have been modified in current object.
     * Tracking modified columns allows us to only update modified columns.
     * @var array
     */
    protected $modifiedColumns = [];

    /**
     * The (virtual) columns that are added at runtime
     * The formatters can add supplementary columns based on a resultset
     * @var array
     */
    protected $virtualColumns = [];

    /**
     * The value for the id field.
     *
     * @var        int
     */
    protected $id;

    /**
     * The value for the family_name field.
     *
     * @var        string
     */
    protected $family_name;

    /**
     * The value for the first_name field.
     *
     * @var        string
     */
    protected $first_name;

    /**
     * The value for the enrolled field.
     *
     * @var        boolean
     */
    protected $enrolled;

    /**
     * The value for the classroom_id field.
     *
     * @var        int
     */
    protected $classroom_id;

    /**
     * @var        ChildClassroom
     */
    protected $aClassroom;

    /**
     * @var        ObjectCollection|ChildAttendance[] Collection to store aggregation of ChildAttendance objects.
     * @phpstan-var ObjectCollection&\Traversable<ChildAttendance> Collection to store aggregation of ChildAttendance objects.
     */
    protected $collAttendances;
    protected $collAttendancesPartial;

    /**
     * @var        ObjectCollection|ChildSchedule[] Collection to store aggregation of ChildSchedule objects.
     * @phpstan-var ObjectCollection&\Traversable<ChildSchedule> Collection to store aggregation of ChildSchedule objects.
     */
    protected $collSchedules;
    protected $collSchedulesPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var bool
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildAttendance[]
     * @phpstan-var ObjectCollection&\Traversable<ChildAttendance>
     */
    protected $attendancesScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildSchedule[]
     * @phpstan-var ObjectCollection&\Traversable<ChildSchedule>
     */
    protected $schedulesScheduledForDeletion = null;

    /**
     * Initializes internal state of flapjack\attend\database\Base\Student object.
     */
    public function __construct()
    {
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return bool True if the object has been modified.
     */
    public function isModified(): bool
    {
        return !!$this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param string $col column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     * @return bool True if $col has been modified.
     */
    public function isColumnModified(string $col): bool
    {
        return $this->modifiedColumns && isset($this->modifiedColumns[$col]);
    }

    /**
     * Get the columns that have been modified in this object.
     * @return array A unique list of the modified column names for this object.
     */
    public function getModifiedColumns(): array
    {
        return $this->modifiedColumns ? array_keys($this->modifiedColumns) : [];
    }

    /**
     * Returns whether the object has ever been saved.  This will
     * be false, if the object was retrieved from storage or was created
     * and then saved.
     *
     * @return bool True, if the object has never been persisted.
     */
    public function isNew(): bool
    {
        return $this->new;
    }

    /**
     * Setter for the isNew attribute.  This method will be called
     * by Propel-generated children and objects.
     *
     * @param bool $b the state of the object.
     */
    public function setNew(bool $b): void
    {
        $this->new = $b;
    }

    /**
     * Whether this object has been deleted.
     * @return bool The deleted state of this object.
     */
    public function isDeleted(): bool
    {
        return $this->deleted;
    }

    /**
     * Specify whether this object has been deleted.
     * @param bool $b The deleted state of this object.
     * @return void
     */
    public function setDeleted(bool $b): void
    {
        $this->deleted = $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param string $col If supplied, only the specified column is reset.
     * @return void
     */
    public function resetModified(?string $col = null): void
    {
        if (null !== $col) {
            unset($this->modifiedColumns[$col]);
        } else {
            $this->modifiedColumns = [];
        }
    }

    /**
     * Compares this with another <code>Student</code> instance.  If
     * <code>obj</code> is an instance of <code>Student</code>, delegates to
     * <code>equals(Student)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param mixed $obj The object to compare to.
     * @return bool Whether equal to the object specified.
     */
    public function equals($obj): bool
    {
        if (!$obj instanceof static) {
            return false;
        }

        if ($this === $obj) {
            return true;
        }

        if (null === $this->getPrimaryKey() || null === $obj->getPrimaryKey()) {
            return false;
        }

        return $this->getPrimaryKey() === $obj->getPrimaryKey();
    }

    /**
     * Get the associative array of the virtual columns in this object
     *
     * @return array
     */
    public function getVirtualColumns(): array
    {
        return $this->virtualColumns;
    }

    /**
     * Checks the existence of a virtual column in this object
     *
     * @param string $name The virtual column name
     * @return bool
     */
    public function hasVirtualColumn(string $name): bool
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param string $name The virtual column name
     * @return mixed
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getVirtualColumn(string $name)
    {
        if (!$this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of nonexistent virtual column `%s`.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param string $name The virtual column name
     * @param mixed $value The value to give to the virtual column
     *
     * @return $this The current object, for fluid interface
     */
    public function setVirtualColumn(string $name, $value)
    {
        $this->virtualColumns[$name] = $value;

        return $this;
    }

    /**
     * Logs a message using Propel::log().
     *
     * @param string $msg
     * @param int $priority One of the Propel::LOG_* logging levels
     * @return void
     */
    protected function log(string $msg, int $priority = Propel::LOG_INFO): void
    {
        Propel::log(get_class($this) . ': ' . $msg, $priority);
    }

    /**
     * Export the current object properties to a string, using a given parser format
     * <code>
     * $book = BookQuery::create()->findPk(9012);
     * echo $book->exportTo('JSON');
     *  => {"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * @param \Propel\Runtime\Parser\AbstractParser|string $parser An AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param bool $includeLazyLoadColumns (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @param string $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME, TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM. Defaults to TableMap::TYPE_PHPNAME.
     * @return string The exported data
     */
    public function exportTo($parser, bool $includeLazyLoadColumns = true, string $keyType = TableMap::TYPE_PHPNAME): string
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        return $parser->fromArray($this->toArray($keyType, $includeLazyLoadColumns, array(), true));
    }

    /**
     * Clean up internal collections prior to serializing
     * Avoids recursive loops that turn into segmentation faults when serializing
     *
     * @return array<string>
     */
    public function __sleep(): array
    {
        $this->clearAllReferences();

        $cls = new \ReflectionClass($this);
        $propertyNames = [];
        $serializableProperties = array_diff($cls->getProperties(), $cls->getProperties(\ReflectionProperty::IS_STATIC));

        foreach($serializableProperties as $property) {
            $propertyNames[] = $property->getName();
        }

        return $propertyNames;
    }

    /**
     * Get the [id] column value.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get the [family_name] column value.
     *
     * @return string
     */
    public function getFamilyName()
    {
        return $this->family_name;
    }

    /**
     * Get the [first_name] column value.
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->first_name;
    }

    /**
     * Get the [enrolled] column value.
     *
     * @return boolean
     */
    public function getEnrolled()
    {
        return $this->enrolled;
    }

    /**
     * Get the [enrolled] column value.
     *
     * @return boolean
     */
    public function isEnrolled()
    {
        return $this->getEnrolled();
    }

    /**
     * Get the [classroom_id] column value.
     *
     * @return int
     */
    public function getClassroomId()
    {
        return $this->classroom_id;
    }

    /**
     * Set the value of [id] column.
     *
     * @param int $v New value
     * @return $this The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id = $v;
            $this->modifiedColumns[StudentTableMap::COL_ID] = true;
        }

        return $this;
    }

    /**
     * Set the value of [family_name] column.
     *
     * @param string $v New value
     * @return $this The current object (for fluent API support)
     */
    public function setFamilyName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->family_name !== $v) {
            $this->family_name = $v;
            $this->modifiedColumns[StudentTableMap::COL_FAMILY_NAME] = true;
        }

        return $this;
    }

    /**
     * Set the value of [first_name] column.
     *
     * @param string $v New value
     * @return $this The current object (for fluent API support)
     */
    public function setFirstName($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->first_name !== $v) {
            $this->first_name = $v;
            $this->modifiedColumns[StudentTableMap::COL_FIRST_NAME] = true;
        }

        return $this;
    }

    /**
     * Sets the value of the [enrolled] column.
     * Non-boolean arguments are converted using the following rules:
     *   * 1, '1', 'true',  'on',  and 'yes' are converted to boolean true
     *   * 0, '0', 'false', 'off', and 'no'  are converted to boolean false
     * Check on string values is case insensitive (so 'FaLsE' is seen as 'false').
     *
     * @param bool|integer|string $v The new value
     * @return $this The current object (for fluent API support)
     */
    public function setEnrolled($v)
    {
        if ($v !== null) {
            if (is_string($v)) {
                $v = in_array(strtolower($v), array('false', 'off', '-', 'no', 'n', '0', '')) ? false : true;
            } else {
                $v = (boolean) $v;
            }
        }

        if ($this->enrolled !== $v) {
            $this->enrolled = $v;
            $this->modifiedColumns[StudentTableMap::COL_ENROLLED] = true;
        }

        return $this;
    }

    /**
     * Set the value of [classroom_id] column.
     *
     * @param int $v New value
     * @return $this The current object (for fluent API support)
     */
    public function setClassroomId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->classroom_id !== $v) {
            $this->classroom_id = $v;
            $this->modifiedColumns[StudentTableMap::COL_CLASSROOM_ID] = true;
        }

        if ($this->aClassroom !== null && $this->aClassroom->getId() !== $v) {
            $this->aClassroom = null;
        }

        return $this;
    }

    /**
     * Indicates whether the columns in this object are only set to default values.
     *
     * This method can be used in conjunction with isModified() to indicate whether an object is both
     * modified _and_ has some values set which are non-default.
     *
     * @return bool Whether the columns in this object are only been set with default values.
     */
    public function hasOnlyDefaultValues(): bool
    {
        // otherwise, everything was equal, so return TRUE
        return true;
    }

    /**
     * Hydrates (populates) the object variables with values from the database resultset.
     *
     * An offset (0-based "start column") is specified so that objects can be hydrated
     * with a subset of the columns in the resultset rows.  This is needed, for example,
     * for results of JOIN queries where the resultset row includes columns from two or
     * more tables.
     *
     * @param array $row The row returned by DataFetcher->fetch().
     * @param int $startcol 0-based offset column which indicates which resultset column to start with.
     * @param bool $rehydrate Whether this object is being re-hydrated from the database.
     * @param string $indexType The index type of $row. Mostly DataFetcher->getIndexType().
                                  One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int next starting column
     * @throws \Propel\Runtime\Exception\PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate(array $row, int $startcol = 0, bool $rehydrate = false, string $indexType = TableMap::TYPE_NUM): int
    {
        try {

            $col = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : StudentTableMap::translateFieldName('Id', TableMap::TYPE_PHPNAME, $indexType)];
            $this->id = (null !== $col) ? (int) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : StudentTableMap::translateFieldName('FamilyName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->family_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : StudentTableMap::translateFieldName('FirstName', TableMap::TYPE_PHPNAME, $indexType)];
            $this->first_name = (null !== $col) ? (string) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 3 + $startcol : StudentTableMap::translateFieldName('Enrolled', TableMap::TYPE_PHPNAME, $indexType)];
            $this->enrolled = (null !== $col) ? (boolean) $col : null;

            $col = $row[TableMap::TYPE_NUM == $indexType ? 4 + $startcol : StudentTableMap::translateFieldName('ClassroomId', TableMap::TYPE_PHPNAME, $indexType)];
            $this->classroom_id = (null !== $col) ? (int) $col : null;

            $this->resetModified();
            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 5; // 5 = StudentTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\flapjack\\attend\\database\\Student'), 0, $e);
        }
    }

    /**
     * Checks and repairs the internal consistency of the object.
     *
     * This method is executed after an already-instantiated object is re-hydrated
     * from the database.  It exists to check any foreign keys to make sure that
     * the objects related to the current object are correct based on foreign key.
     *
     * You can override this method in the stub class, but you should always invoke
     * the base method from the overridden method (i.e. parent::ensureConsistency()),
     * in case your model changes.
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function ensureConsistency(): void
    {
        if ($this->aClassroom !== null && $this->classroom_id !== $this->aClassroom->getId()) {
            $this->aClassroom = null;
        }
    }

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param bool $deep (optional) Whether to also de-associated any related objects.
     * @param ConnectionInterface $con (optional) The ConnectionInterface connection to use.
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException - if this object is deleted, unsaved or doesn't have pk match in db
     */
    public function reload(bool $deep = false, ?ConnectionInterface $con = null): void
    {
        if ($this->isDeleted()) {
            throw new PropelException("Cannot reload a deleted object.");
        }

        if ($this->isNew()) {
            throw new PropelException("Cannot reload an unsaved object.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getReadConnection(StudentTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildStudentQuery::create(null, $this->buildPkeyCriteria())->setFormatter(ModelCriteria::FORMAT_STATEMENT)->find($con);
        $row = $dataFetcher->fetch();
        $dataFetcher->close();
        if (!$row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->aClassroom = null;
            $this->collAttendances = null;

            $this->collSchedules = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param ConnectionInterface $con
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @see Student::setDeleted()
     * @see Student::isDeleted()
     */
    public function delete(?ConnectionInterface $con = null): void
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildStudentQuery::create()
                ->filterByPrimaryKey($this->getPrimaryKey());
            $ret = $this->preDelete($con);
            if ($ret) {
                $deleteQuery->delete($con);
                $this->postDelete($con);
                $this->setDeleted(true);
            }
        });
    }

    /**
     * Persists this object to the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All modified related objects will also be persisted in the doSave()
     * method.  This method wraps all precipitate database operations in a
     * single transaction.
     *
     * @param ConnectionInterface $con
     * @return int The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws \Propel\Runtime\Exception\PropelException
     * @see doSave()
     */
    public function save(?ConnectionInterface $con = null): int
    {
        if ($this->isDeleted()) {
            throw new PropelException("You cannot save an object that has been deleted.");
        }

        if ($this->alreadyInSave) {
            return 0;
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(StudentTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret = $this->preSave($con);
            $isInsert = $this->isNew();
            if ($isInsert) {
                $ret = $ret && $this->preInsert($con);
            } else {
                $ret = $ret && $this->preUpdate($con);
            }
            if ($ret) {
                $affectedRows = $this->doSave($con);
                if ($isInsert) {
                    $this->postInsert($con);
                } else {
                    $this->postUpdate($con);
                }
                $this->postSave($con);
                StudentTableMap::addInstanceToPool($this);
            } else {
                $affectedRows = 0;
            }

            return $affectedRows;
        });
    }

    /**
     * Performs the work of inserting or updating the row in the database.
     *
     * If the object is new, it inserts it; otherwise an update is performed.
     * All related objects are also updated in this method.
     *
     * @param ConnectionInterface $con
     * @return int The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws \Propel\Runtime\Exception\PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con): int
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if (!$this->alreadyInSave) {
            $this->alreadyInSave = true;

            // We call the save method on the following object(s) if they
            // were passed to this object by their corresponding set
            // method.  This object relates to these object(s) by a
            // foreign key reference.

            if ($this->aClassroom !== null) {
                if ($this->aClassroom->isModified() || $this->aClassroom->isNew()) {
                    $affectedRows += $this->aClassroom->save($con);
                }
                $this->setClassroom($this->aClassroom);
            }

            if ($this->isNew() || $this->isModified()) {
                // persist changes
                if ($this->isNew()) {
                    $this->doInsert($con);
                    $affectedRows += 1;
                } else {
                    $affectedRows += $this->doUpdate($con);
                }
                $this->resetModified();
            }

            if ($this->attendancesScheduledForDeletion !== null) {
                if (!$this->attendancesScheduledForDeletion->isEmpty()) {
                    \flapjack\attend\database\AttendanceQuery::create()
                        ->filterByPrimaryKeys($this->attendancesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->attendancesScheduledForDeletion = null;
                }
            }

            if ($this->collAttendances !== null) {
                foreach ($this->collAttendances as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->schedulesScheduledForDeletion !== null) {
                if (!$this->schedulesScheduledForDeletion->isEmpty()) {
                    \flapjack\attend\database\ScheduleQuery::create()
                        ->filterByPrimaryKeys($this->schedulesScheduledForDeletion->getPrimaryKeys(false))
                        ->delete($con);
                    $this->schedulesScheduledForDeletion = null;
                }
            }

            if ($this->collSchedules !== null) {
                foreach ($this->collSchedules as $referrerFK) {
                    if (!$referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            $this->alreadyInSave = false;

        }

        return $affectedRows;
    }

    /**
     * Insert the row in the database.
     *
     * @param ConnectionInterface $con
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con): void
    {
        $modifiedColumns = [];
        $index = 0;

        $this->modifiedColumns[StudentTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . StudentTableMap::COL_ID . ')');
        }

         // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(StudentTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(StudentTableMap::COL_FAMILY_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'family_name';
        }
        if ($this->isColumnModified(StudentTableMap::COL_FIRST_NAME)) {
            $modifiedColumns[':p' . $index++]  = 'first_name';
        }
        if ($this->isColumnModified(StudentTableMap::COL_ENROLLED)) {
            $modifiedColumns[':p' . $index++]  = 'enrolled';
        }
        if ($this->isColumnModified(StudentTableMap::COL_CLASSROOM_ID)) {
            $modifiedColumns[':p' . $index++]  = 'classroom_id';
        }

        $sql = sprintf(
            'INSERT INTO students (%s) VALUES (%s)',
            implode(', ', $modifiedColumns),
            implode(', ', array_keys($modifiedColumns))
        );

        try {
            $stmt = $con->prepare($sql);
            foreach ($modifiedColumns as $identifier => $columnName) {
                switch ($columnName) {
                    case 'id':
                        $stmt->bindValue($identifier, $this->id, PDO::PARAM_INT);

                        break;
                    case 'family_name':
                        $stmt->bindValue($identifier, $this->family_name, PDO::PARAM_STR);

                        break;
                    case 'first_name':
                        $stmt->bindValue($identifier, $this->first_name, PDO::PARAM_STR);

                        break;
                    case 'enrolled':
                        $stmt->bindValue($identifier, (int) $this->enrolled, PDO::PARAM_INT);

                        break;
                    case 'classroom_id':
                        $stmt->bindValue($identifier, $this->classroom_id, PDO::PARAM_INT);

                        break;
                }
            }
            $stmt->execute();
        } catch (Exception $e) {
            Propel::log($e->getMessage(), Propel::LOG_ERR);
            throw new PropelException(sprintf('Unable to execute INSERT statement [%s]', $sql), 0, $e);
        }

        try {
            $pk = $con->lastInsertId();
        } catch (Exception $e) {
            throw new PropelException('Unable to get autoincrement id.', 0, $e);
        }
        $this->setId($pk);

        $this->setNew(false);
    }

    /**
     * Update the row in the database.
     *
     * @param ConnectionInterface $con
     *
     * @return int Number of updated rows
     * @see doSave()
     */
    protected function doUpdate(ConnectionInterface $con): int
    {
        $selectCriteria = $this->buildPkeyCriteria();
        $valuesCriteria = $this->buildCriteria();

        return $selectCriteria->doUpdate($valuesCriteria, $con);
    }

    /**
     * Retrieves a field from the object by name passed in as a string.
     *
     * @param string $name name
     * @param string $type The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName(string $name, string $type = TableMap::TYPE_PHPNAME)
    {
        $pos = StudentTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos Position in XML schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition(int $pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();

            case 1:
                return $this->getFamilyName();

            case 2:
                return $this->getFirstName();

            case 3:
                return $this->getEnrolled();

            case 4:
                return $this->getClassroomId();

            default:
                return null;
        } // switch()
    }

    /**
     * Exports the object as an array.
     *
     * You can specify the key type of the array by passing one of the class
     * type constants.
     *
     * @param string $keyType (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param bool $includeLazyLoadColumns (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param array $alreadyDumpedObjects List of objects to skip to avoid recursion
     * @param bool $includeForeignObjects (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array An associative array containing the field names (as keys) and field values
     */
    public function toArray(string $keyType = TableMap::TYPE_PHPNAME, bool $includeLazyLoadColumns = true, array $alreadyDumpedObjects = [], bool $includeForeignObjects = false): array
    {
        if (isset($alreadyDumpedObjects['Student'][$this->hashCode()])) {
            return ['*RECURSION*'];
        }
        $alreadyDumpedObjects['Student'][$this->hashCode()] = true;
        $keys = StudentTableMap::getFieldNames($keyType);
        $result = [
            $keys[0] => $this->getId(),
            $keys[1] => $this->getFamilyName(),
            $keys[2] => $this->getFirstName(),
            $keys[3] => $this->getEnrolled(),
            $keys[4] => $this->getClassroomId(),
        ];
        $virtualColumns = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->aClassroom) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'classroom';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'classrooms';
                        break;
                    default:
                        $key = 'Classroom';
                }

                $result[$key] = $this->aClassroom->toArray($keyType, $includeLazyLoadColumns,  $alreadyDumpedObjects, true);
            }
            if (null !== $this->collAttendances) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'attendances';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'attendances';
                        break;
                    default:
                        $key = 'Attendances';
                }

                $result[$key] = $this->collAttendances->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
            if (null !== $this->collSchedules) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'schedules';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'scheduless';
                        break;
                    default:
                        $key = 'Schedules';
                }

                $result[$key] = $this->collSchedules->toArray(null, false, $keyType, $includeLazyLoadColumns, $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param string $name
     * @param mixed $value field value
     * @param string $type The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this
     */
    public function setByName(string $name, $value, string $type = TableMap::TYPE_PHPNAME)
    {
        $pos = StudentTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        $this->setByPosition($pos, $value);

        return $this;
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param int $pos position in xml schema
     * @param mixed $value field value
     * @return $this
     */
    public function setByPosition(int $pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setFamilyName($value);
                break;
            case 2:
                $this->setFirstName($value);
                break;
            case 3:
                $this->setEnrolled($value);
                break;
            case 4:
                $this->setClassroomId($value);
                break;
        } // switch()

        return $this;
    }

    /**
     * Populates the object using an array.
     *
     * This is particularly useful when populating an object from one of the
     * request arrays (e.g. $_POST).  This method goes through the column
     * names, checking to see whether a matching key exists in populated
     * array. If so the setByName() method is called for that column.
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param array $arr An array to populate the object from.
     * @param string $keyType The type of keys the array uses.
     * @return $this
     */
    public function fromArray(array $arr, string $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = StudentTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setFamilyName($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setFirstName($arr[$keys[2]]);
        }
        if (array_key_exists($keys[3], $arr)) {
            $this->setEnrolled($arr[$keys[3]]);
        }
        if (array_key_exists($keys[4], $arr)) {
            $this->setClassroomId($arr[$keys[4]]);
        }

        return $this;
    }

     /**
     * Populate the current object from a string, using a given parser format
     * <code>
     * $book = new Book();
     * $book->importFrom('JSON', '{"Id":9012,"Title":"Don Juan","ISBN":"0140422161","Price":12.99,"PublisherId":1234,"AuthorId":5678}');
     * </code>
     *
     * You can specify the key type of the array by additionally passing one
     * of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     * TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     * The default key type is the column's TableMap::TYPE_PHPNAME.
     *
     * @param mixed $parser A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param string $data The source data to import from
     * @param string $keyType The type of keys the array uses.
     *
     * @return $this The current object, for fluid interface
     */
    public function importFrom($parser, string $data, string $keyType = TableMap::TYPE_PHPNAME)
    {
        if (!$parser instanceof AbstractParser) {
            $parser = AbstractParser::getParser($parser);
        }

        $this->fromArray($parser->toArray($data), $keyType);

        return $this;
    }

    /**
     * Build a Criteria object containing the values of all modified columns in this object.
     *
     * @return \Propel\Runtime\ActiveQuery\Criteria The Criteria object containing all modified values.
     */
    public function buildCriteria(): Criteria
    {
        $criteria = new Criteria(StudentTableMap::DATABASE_NAME);

        if ($this->isColumnModified(StudentTableMap::COL_ID)) {
            $criteria->add(StudentTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(StudentTableMap::COL_FAMILY_NAME)) {
            $criteria->add(StudentTableMap::COL_FAMILY_NAME, $this->family_name);
        }
        if ($this->isColumnModified(StudentTableMap::COL_FIRST_NAME)) {
            $criteria->add(StudentTableMap::COL_FIRST_NAME, $this->first_name);
        }
        if ($this->isColumnModified(StudentTableMap::COL_ENROLLED)) {
            $criteria->add(StudentTableMap::COL_ENROLLED, $this->enrolled);
        }
        if ($this->isColumnModified(StudentTableMap::COL_CLASSROOM_ID)) {
            $criteria->add(StudentTableMap::COL_CLASSROOM_ID, $this->classroom_id);
        }

        return $criteria;
    }

    /**
     * Builds a Criteria object containing the primary key for this object.
     *
     * Unlike buildCriteria() this method includes the primary key values regardless
     * of whether they have been modified.
     *
     * @throws LogicException if no primary key is defined
     *
     * @return \Propel\Runtime\ActiveQuery\Criteria The Criteria object containing value(s) for primary key(s).
     */
    public function buildPkeyCriteria(): Criteria
    {
        $criteria = ChildStudentQuery::create();
        $criteria->add(StudentTableMap::COL_ID, $this->id);

        return $criteria;
    }

    /**
     * If the primary key is not null, return the hashcode of the
     * primary key. Otherwise, return the hash code of the object.
     *
     * @return int|string Hashcode
     */
    public function hashCode()
    {
        $validPk = null !== $this->getId();

        $validPrimaryKeyFKs = 0;
        $primaryKeyFKs = [];

        if ($validPk) {
            return crc32(json_encode($this->getPrimaryKey(), JSON_UNESCAPED_UNICODE));
        } elseif ($validPrimaryKeyFKs) {
            return crc32(json_encode($primaryKeyFKs, JSON_UNESCAPED_UNICODE));
        }

        return spl_object_hash($this);
    }

    /**
     * Returns the primary key for this object (row).
     * @return int
     */
    public function getPrimaryKey()
    {
        return $this->getId();
    }

    /**
     * Generic method to set the primary key (id column).
     *
     * @param int|null $key Primary key.
     * @return void
     */
    public function setPrimaryKey(?int $key = null): void
    {
        $this->setId($key);
    }

    /**
     * Returns true if the primary key for this object is null.
     *
     * @return bool
     */
    public function isPrimaryKeyNull(): bool
    {
        return null === $this->getId();
    }

    /**
     * Sets contents of passed object to values from current object.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param object $copyObj An object of \flapjack\attend\database\Student (or compatible) type.
     * @param bool $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @param bool $makeNew Whether to reset autoincrement PKs and make the object new.
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function copyInto(object $copyObj, bool $deepCopy = false, bool $makeNew = true): void
    {
        $copyObj->setFamilyName($this->getFamilyName());
        $copyObj->setFirstName($this->getFirstName());
        $copyObj->setEnrolled($this->getEnrolled());
        $copyObj->setClassroomId($this->getClassroomId());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getAttendances() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addAttendance($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getSchedules() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addSchedule($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(NULL); // this is a auto-increment column, so set to default value
        }
    }

    /**
     * Makes a copy of this object that will be inserted as a new row in table when saved.
     * It creates a new object filling in the simple attributes, but skipping any primary
     * keys that are defined for the table.
     *
     * If desired, this method can also make copies of all associated (fkey referrers)
     * objects.
     *
     * @param bool $deepCopy Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \flapjack\attend\database\Student Clone of current object.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function copy(bool $deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }

    /**
     * Declares an association between this object and a ChildClassroom object.
     *
     * @param ChildClassroom $v
     * @return $this The current object (for fluent API support)
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function setClassroom(ChildClassroom $v = null)
    {
        if ($v === null) {
            $this->setClassroomId(NULL);
        } else {
            $this->setClassroomId($v->getId());
        }

        $this->aClassroom = $v;

        // Add binding for other direction of this n:n relationship.
        // If this object has already been added to the ChildClassroom object, it will not be re-added.
        if ($v !== null) {
            $v->addStudent($this);
        }


        return $this;
    }


    /**
     * Get the associated ChildClassroom object
     *
     * @param ConnectionInterface $con Optional Connection object.
     * @return ChildClassroom The associated ChildClassroom object.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getClassroom(?ConnectionInterface $con = null)
    {
        if ($this->aClassroom === null && ($this->classroom_id != 0)) {
            $this->aClassroom = ChildClassroomQuery::create()->findPk($this->classroom_id, $con);
            /* The following can be used additionally to
                guarantee the related object contains a reference
                to this object.  This level of coupling may, however, be
                undesirable since it could result in an only partially populated collection
                in the referenced object.
                $this->aClassroom->addStudents($this);
             */
        }

        return $this->aClassroom;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param string $relationName The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName): void
    {
        if ('Attendance' === $relationName) {
            $this->initAttendances();
            return;
        }
        if ('Schedule' === $relationName) {
            $this->initSchedules();
            return;
        }
    }

    /**
     * Clears out the collAttendances collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return $this
     * @see addAttendances()
     */
    public function clearAttendances()
    {
        $this->collAttendances = null; // important to set this to NULL since that means it is uninitialized

        return $this;
    }

    /**
     * Reset is the collAttendances collection loaded partially.
     *
     * @return void
     */
    public function resetPartialAttendances($v = true): void
    {
        $this->collAttendancesPartial = $v;
    }

    /**
     * Initializes the collAttendances collection.
     *
     * By default this just sets the collAttendances collection to an empty array (like clearcollAttendances());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param bool $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initAttendances(bool $overrideExisting = true): void
    {
        if (null !== $this->collAttendances && !$overrideExisting) {
            return;
        }

        $collectionClassName = AttendanceTableMap::getTableMap()->getCollectionClassName();

        $this->collAttendances = new $collectionClassName;
        $this->collAttendances->setModel('\flapjack\attend\database\Attendance');
    }

    /**
     * Gets an array of ChildAttendance objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildStudent is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildAttendance[] List of ChildAttendance objects
     * @phpstan-return ObjectCollection&\Traversable<ChildAttendance> List of ChildAttendance objects
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getAttendances(?Criteria $criteria = null, ?ConnectionInterface $con = null)
    {
        $partial = $this->collAttendancesPartial && !$this->isNew();
        if (null === $this->collAttendances || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collAttendances) {
                    $this->initAttendances();
                } else {
                    $collectionClassName = AttendanceTableMap::getTableMap()->getCollectionClassName();

                    $collAttendances = new $collectionClassName;
                    $collAttendances->setModel('\flapjack\attend\database\Attendance');

                    return $collAttendances;
                }
            } else {
                $collAttendances = ChildAttendanceQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collAttendancesPartial && count($collAttendances)) {
                        $this->initAttendances(false);

                        foreach ($collAttendances as $obj) {
                            if (false == $this->collAttendances->contains($obj)) {
                                $this->collAttendances->append($obj);
                            }
                        }

                        $this->collAttendancesPartial = true;
                    }

                    return $collAttendances;
                }

                if ($partial && $this->collAttendances) {
                    foreach ($this->collAttendances as $obj) {
                        if ($obj->isNew()) {
                            $collAttendances[] = $obj;
                        }
                    }
                }

                $this->collAttendances = $collAttendances;
                $this->collAttendancesPartial = false;
            }
        }

        return $this->collAttendances;
    }

    /**
     * Sets a collection of ChildAttendance objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection $attendances A Propel collection.
     * @param ConnectionInterface $con Optional connection object
     * @return $this The current object (for fluent API support)
     */
    public function setAttendances(Collection $attendances, ?ConnectionInterface $con = null)
    {
        /** @var ChildAttendance[] $attendancesToDelete */
        $attendancesToDelete = $this->getAttendances(new Criteria(), $con)->diff($attendances);


        $this->attendancesScheduledForDeletion = $attendancesToDelete;

        foreach ($attendancesToDelete as $attendanceRemoved) {
            $attendanceRemoved->setStudent(null);
        }

        $this->collAttendances = null;
        foreach ($attendances as $attendance) {
            $this->addAttendance($attendance);
        }

        $this->collAttendances = $attendances;
        $this->collAttendancesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Attendance objects.
     *
     * @param Criteria $criteria
     * @param bool $distinct
     * @param ConnectionInterface $con
     * @return int Count of related Attendance objects.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function countAttendances(?Criteria $criteria = null, bool $distinct = false, ?ConnectionInterface $con = null): int
    {
        $partial = $this->collAttendancesPartial && !$this->isNew();
        if (null === $this->collAttendances || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collAttendances) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getAttendances());
            }

            $query = ChildAttendanceQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collAttendances);
    }

    /**
     * Method called to associate a ChildAttendance object to this object
     * through the ChildAttendance foreign key attribute.
     *
     * @param ChildAttendance $l ChildAttendance
     * @return $this The current object (for fluent API support)
     */
    public function addAttendance(ChildAttendance $l)
    {
        if ($this->collAttendances === null) {
            $this->initAttendances();
            $this->collAttendancesPartial = true;
        }

        if (!$this->collAttendances->contains($l)) {
            $this->doAddAttendance($l);

            if ($this->attendancesScheduledForDeletion and $this->attendancesScheduledForDeletion->contains($l)) {
                $this->attendancesScheduledForDeletion->remove($this->attendancesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildAttendance $attendance The ChildAttendance object to add.
     */
    protected function doAddAttendance(ChildAttendance $attendance): void
    {
        $this->collAttendances[]= $attendance;
        $attendance->setStudent($this);
    }

    /**
     * @param ChildAttendance $attendance The ChildAttendance object to remove.
     * @return $this The current object (for fluent API support)
     */
    public function removeAttendance(ChildAttendance $attendance)
    {
        if ($this->getAttendances()->contains($attendance)) {
            $pos = $this->collAttendances->search($attendance);
            $this->collAttendances->remove($pos);
            if (null === $this->attendancesScheduledForDeletion) {
                $this->attendancesScheduledForDeletion = clone $this->collAttendances;
                $this->attendancesScheduledForDeletion->clear();
            }
            $this->attendancesScheduledForDeletion[]= clone $attendance;
            $attendance->setStudent(null);
        }

        return $this;
    }

    /**
     * Clears out the collSchedules collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return $this
     * @see addSchedules()
     */
    public function clearSchedules()
    {
        $this->collSchedules = null; // important to set this to NULL since that means it is uninitialized

        return $this;
    }

    /**
     * Reset is the collSchedules collection loaded partially.
     *
     * @return void
     */
    public function resetPartialSchedules($v = true): void
    {
        $this->collSchedulesPartial = $v;
    }

    /**
     * Initializes the collSchedules collection.
     *
     * By default this just sets the collSchedules collection to an empty array (like clearcollSchedules());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param bool $overrideExisting If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initSchedules(bool $overrideExisting = true): void
    {
        if (null !== $this->collSchedules && !$overrideExisting) {
            return;
        }

        $collectionClassName = ScheduleTableMap::getTableMap()->getCollectionClassName();

        $this->collSchedules = new $collectionClassName;
        $this->collSchedules->setModel('\flapjack\attend\database\Schedule');
    }

    /**
     * Gets an array of ChildSchedule objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildStudent is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param Criteria $criteria optional Criteria object to narrow the query
     * @param ConnectionInterface $con optional connection object
     * @return ObjectCollection|ChildSchedule[] List of ChildSchedule objects
     * @phpstan-return ObjectCollection&\Traversable<ChildSchedule> List of ChildSchedule objects
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getSchedules(?Criteria $criteria = null, ?ConnectionInterface $con = null)
    {
        $partial = $this->collSchedulesPartial && !$this->isNew();
        if (null === $this->collSchedules || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collSchedules) {
                    $this->initSchedules();
                } else {
                    $collectionClassName = ScheduleTableMap::getTableMap()->getCollectionClassName();

                    $collSchedules = new $collectionClassName;
                    $collSchedules->setModel('\flapjack\attend\database\Schedule');

                    return $collSchedules;
                }
            } else {
                $collSchedules = ChildScheduleQuery::create(null, $criteria)
                    ->filterByStudent($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collSchedulesPartial && count($collSchedules)) {
                        $this->initSchedules(false);

                        foreach ($collSchedules as $obj) {
                            if (false == $this->collSchedules->contains($obj)) {
                                $this->collSchedules->append($obj);
                            }
                        }

                        $this->collSchedulesPartial = true;
                    }

                    return $collSchedules;
                }

                if ($partial && $this->collSchedules) {
                    foreach ($this->collSchedules as $obj) {
                        if ($obj->isNew()) {
                            $collSchedules[] = $obj;
                        }
                    }
                }

                $this->collSchedules = $collSchedules;
                $this->collSchedulesPartial = false;
            }
        }

        return $this->collSchedules;
    }

    /**
     * Sets a collection of ChildSchedule objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param Collection $schedules A Propel collection.
     * @param ConnectionInterface $con Optional connection object
     * @return $this The current object (for fluent API support)
     */
    public function setSchedules(Collection $schedules, ?ConnectionInterface $con = null)
    {
        /** @var ChildSchedule[] $schedulesToDelete */
        $schedulesToDelete = $this->getSchedules(new Criteria(), $con)->diff($schedules);


        $this->schedulesScheduledForDeletion = $schedulesToDelete;

        foreach ($schedulesToDelete as $scheduleRemoved) {
            $scheduleRemoved->setStudent(null);
        }

        $this->collSchedules = null;
        foreach ($schedules as $schedule) {
            $this->addSchedule($schedule);
        }

        $this->collSchedules = $schedules;
        $this->collSchedulesPartial = false;

        return $this;
    }

    /**
     * Returns the number of related Schedule objects.
     *
     * @param Criteria $criteria
     * @param bool $distinct
     * @param ConnectionInterface $con
     * @return int Count of related Schedule objects.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function countSchedules(?Criteria $criteria = null, bool $distinct = false, ?ConnectionInterface $con = null): int
    {
        $partial = $this->collSchedulesPartial && !$this->isNew();
        if (null === $this->collSchedules || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collSchedules) {
                return 0;
            }

            if ($partial && !$criteria) {
                return count($this->getSchedules());
            }

            $query = ChildScheduleQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByStudent($this)
                ->count($con);
        }

        return count($this->collSchedules);
    }

    /**
     * Method called to associate a ChildSchedule object to this object
     * through the ChildSchedule foreign key attribute.
     *
     * @param ChildSchedule $l ChildSchedule
     * @return $this The current object (for fluent API support)
     */
    public function addSchedule(ChildSchedule $l)
    {
        if ($this->collSchedules === null) {
            $this->initSchedules();
            $this->collSchedulesPartial = true;
        }

        if (!$this->collSchedules->contains($l)) {
            $this->doAddSchedule($l);

            if ($this->schedulesScheduledForDeletion and $this->schedulesScheduledForDeletion->contains($l)) {
                $this->schedulesScheduledForDeletion->remove($this->schedulesScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param ChildSchedule $schedule The ChildSchedule object to add.
     */
    protected function doAddSchedule(ChildSchedule $schedule): void
    {
        $this->collSchedules[]= $schedule;
        $schedule->setStudent($this);
    }

    /**
     * @param ChildSchedule $schedule The ChildSchedule object to remove.
     * @return $this The current object (for fluent API support)
     */
    public function removeSchedule(ChildSchedule $schedule)
    {
        if ($this->getSchedules()->contains($schedule)) {
            $pos = $this->collSchedules->search($schedule);
            $this->collSchedules->remove($pos);
            if (null === $this->schedulesScheduledForDeletion) {
                $this->schedulesScheduledForDeletion = clone $this->collSchedules;
                $this->schedulesScheduledForDeletion->clear();
            }
            $this->schedulesScheduledForDeletion[]= clone $schedule;
            $schedule->setStudent(null);
        }

        return $this;
    }

    /**
     * Clears the current object, sets all attributes to their default values and removes
     * outgoing references as well as back-references (from other objects to this one. Results probably in a database
     * change of those foreign objects when you call `save` there).
     *
     * @return $this
     */
    public function clear()
    {
        if (null !== $this->aClassroom) {
            $this->aClassroom->removeStudent($this);
        }
        $this->id = null;
        $this->family_name = null;
        $this->first_name = null;
        $this->enrolled = null;
        $this->classroom_id = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->resetModified();
        $this->setNew(true);
        $this->setDeleted(false);

        return $this;
    }

    /**
     * Resets all references and back-references to other model objects or collections of model objects.
     *
     * This method is used to reset all php object references (not the actual reference in the database).
     * Necessary for object serialisation.
     *
     * @param bool $deep Whether to also clear the references on all referrer objects.
     * @return $this
     */
    public function clearAllReferences(bool $deep = false)
    {
        if ($deep) {
            if ($this->collAttendances) {
                foreach ($this->collAttendances as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collSchedules) {
                foreach ($this->collSchedules as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collAttendances = null;
        $this->collSchedules = null;
        $this->aClassroom = null;
        return $this;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->exportTo(StudentTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param ConnectionInterface|null $con
     * @return bool
     */
    public function preSave(?ConnectionInterface $con = null): bool
    {
                return true;
    }

    /**
     * Code to be run after persisting the object
     * @param ConnectionInterface|null $con
     * @return void
     */
    public function postSave(?ConnectionInterface $con = null): void
    {
            }

    /**
     * Code to be run before inserting to database
     * @param ConnectionInterface|null $con
     * @return bool
     */
    public function preInsert(?ConnectionInterface $con = null): bool
    {
                return true;
    }

    /**
     * Code to be run after inserting to database
     * @param ConnectionInterface|null $con
     * @return void
     */
    public function postInsert(?ConnectionInterface $con = null): void
    {
            }

    /**
     * Code to be run before updating the object in database
     * @param ConnectionInterface|null $con
     * @return bool
     */
    public function preUpdate(?ConnectionInterface $con = null): bool
    {
                return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param ConnectionInterface|null $con
     * @return void
     */
    public function postUpdate(?ConnectionInterface $con = null): void
    {
            }

    /**
     * Code to be run before deleting the object in database
     * @param ConnectionInterface|null $con
     * @return bool
     */
    public function preDelete(?ConnectionInterface $con = null): bool
    {
                return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param ConnectionInterface|null $con
     * @return void
     */
    public function postDelete(?ConnectionInterface $con = null): void
    {
            }


    /**
     * Derived method to catches calls to undefined methods.
     *
     * Provides magic import/export method support (fromXML()/toXML(), fromYAML()/toYAML(), etc.).
     * Allows to define default __call() behavior if you overwrite __call()
     *
     * @param string $name
     * @param mixed $params
     *
     * @return array|string
     */
    public function __call($name, $params)
    {
        if (0 === strpos($name, 'get')) {
            $virtualColumn = substr($name, 3);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }

            $virtualColumn = lcfirst($virtualColumn);
            if ($this->hasVirtualColumn($virtualColumn)) {
                return $this->getVirtualColumn($virtualColumn);
            }
        }

        if (0 === strpos($name, 'from')) {
            $format = substr($name, 4);
            $inputData = $params[0];
            $keyType = $params[1] ?? TableMap::TYPE_PHPNAME;

            return $this->importFrom($format, $inputData, $keyType);
        }

        if (0 === strpos($name, 'to')) {
            $format = substr($name, 2);
            $includeLazyLoadColumns = $params[0] ?? true;
            $keyType = $params[1] ?? TableMap::TYPE_PHPNAME;

            return $this->exportTo($format, $includeLazyLoadColumns, $keyType);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
