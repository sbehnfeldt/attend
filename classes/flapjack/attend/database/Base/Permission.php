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
use flapjack\attend\database\GroupPermission as ChildGroupPermission;
use flapjack\attend\database\GroupPermissionQuery as ChildGroupPermissionQuery;
use flapjack\attend\database\IndividualPermission as ChildIndividualPermission;
use flapjack\attend\database\IndividualPermissionQuery as ChildIndividualPermissionQuery;
use flapjack\attend\database\Permission as ChildPermission;
use flapjack\attend\database\PermissionQuery as ChildPermissionQuery;
use flapjack\attend\database\Map\GroupPermissionTableMap;
use flapjack\attend\database\Map\IndividualPermissionTableMap;
use flapjack\attend\database\Map\PermissionTableMap;

/**
 * Base class that represents a row from the 'permissions' table.
 *
 *
 *
 * @package    propel.generator..Base
 */
abstract class Permission implements ActiveRecordInterface
{
    /**
     * TableMap class name
     *
     * @var string
     */
    public const TABLE_MAP = '\\flapjack\\attend\\database\\Map\\PermissionTableMap';


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
     * The value for the slug field.
     * Human-readable mnemonic for the permission name
     * @var        string
     */
    protected $slug;

    /**
     * The value for the description field.
     * Description of what the permission permits
     * Note: this column has a database default value of: ''
     * @var        string
     */
    protected $description;

    /**
     * @var        ObjectCollection|ChildGroupPermission[] Collection to store aggregation of ChildGroupPermission objects.
     * @phpstan-var ObjectCollection&\Traversable<ChildGroupPermission> Collection to store aggregation of ChildGroupPermission objects.
     */
    protected $collGroupPermissions;
    protected $collGroupPermissionsPartial;

    /**
     * @var        ObjectCollection|ChildIndividualPermission[] Collection to store aggregation of ChildIndividualPermission objects.
     * @phpstan-var ObjectCollection&\Traversable<ChildIndividualPermission> Collection to store aggregation of ChildIndividualPermission objects.
     */
    protected $collIndividualPermissions;
    protected $collIndividualPermissionsPartial;

    /**
     * Flag to prevent endless save loop, if this object is referenced
     * by another object which falls in this transaction.
     *
     * @var bool
     */
    protected $alreadyInSave = false;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildGroupPermission[]
     * @phpstan-var ObjectCollection&\Traversable<ChildGroupPermission>
     */
    protected $groupPermissionsScheduledForDeletion = null;

    /**
     * An array of objects scheduled for deletion.
     * @var ObjectCollection|ChildIndividualPermission[]
     * @phpstan-var ObjectCollection&\Traversable<ChildIndividualPermission>
     */
    protected $individualPermissionsScheduledForDeletion = null;

    /**
     * Applies default values to this object.
     * This method should be called from the object's constructor (or
     * equivalent initialization method).
     * @see __construct()
     */
    public function applyDefaultValues(): void
    {
        $this->description = '';
    }

    /**
     * Initializes internal state of flapjack\attend\database\Base\Permission object.
     * @see applyDefaults()
     */
    public function __construct()
    {
        $this->applyDefaultValues();
    }

    /**
     * Returns whether the object has been modified.
     *
     * @return bool True if the object has been modified.
     */
    public function isModified(): bool
    {
        return ! ! $this->modifiedColumns;
    }

    /**
     * Has specified column been modified?
     *
     * @param  string  $col  column fully qualified name (TableMap::TYPE_COLNAME), e.g. Book::AUTHOR_ID
     *
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
     * @param  bool  $b  the state of the object.
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
     * @param  bool  $b  The deleted state of this object.
     * @return void
     */
    public function setDeleted(bool $b): void
    {
        $this->deleted = $b;
    }

    /**
     * Sets the modified state for the object to be false.
     * @param  string  $col  If supplied, only the specified column is reset.
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
     * Compares this with another <code>Permission</code> instance.  If
     * <code>obj</code> is an instance of <code>Permission</code>, delegates to
     * <code>equals(Permission)</code>.  Otherwise, returns <code>false</code>.
     *
     * @param  mixed  $obj  The object to compare to.
     * @return bool Whether equal to the object specified.
     */
    public function equals($obj): bool
    {
        if ( ! $obj instanceof static) {
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
     * @param  string  $name  The virtual column name
     * @return bool
     */
    public function hasVirtualColumn(string $name): bool
    {
        return array_key_exists($name, $this->virtualColumns);
    }

    /**
     * Get the value of a virtual column in this object
     *
     * @param  string  $name  The virtual column name
     * @return mixed
     *
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getVirtualColumn(string $name)
    {
        if ( ! $this->hasVirtualColumn($name)) {
            throw new PropelException(sprintf('Cannot get value of nonexistent virtual column `%s`.', $name));
        }

        return $this->virtualColumns[$name];
    }

    /**
     * Set the value of a virtual column in this object
     *
     * @param  string  $name  The virtual column name
     * @param  mixed  $value  The value to give to the virtual column
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
     * @param  string  $msg
     * @param  int  $priority  One of the Propel::LOG_* logging levels
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
     * @param  \Propel\Runtime\Parser\AbstractParser|string  $parser  An AbstractParser instance, or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  bool  $includeLazyLoadColumns  (optional) Whether to include lazy load(ed) columns. Defaults to TRUE.
     * @param  string  $keyType  (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME, TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM. Defaults to TableMap::TYPE_PHPNAME.
     *
     * @return string The exported data
     */
    public function exportTo(
        $parser,
        bool $includeLazyLoadColumns = true,
        string $keyType = TableMap::TYPE_PHPNAME
    ): string {
        if ( ! $parser instanceof AbstractParser) {
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

        $cls                    = new \ReflectionClass($this);
        $propertyNames          = [];
        $serializableProperties = array_diff(
            $cls->getProperties(),
            $cls->getProperties(\ReflectionProperty::IS_STATIC)
        );

        foreach ($serializableProperties as $property) {
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
     * Get the [slug] column value.
     * Human-readable mnemonic for the permission name
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Get the [description] column value.
     * Description of what the permission permits
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of [id] column.
     *
     * @param  int  $v  New value
     * @return $this The current object (for fluent API support)
     */
    public function setId($v)
    {
        if ($v !== null) {
            $v = (int) $v;
        }

        if ($this->id !== $v) {
            $this->id                                          = $v;
            $this->modifiedColumns[PermissionTableMap::COL_ID] = true;
        }

        return $this;
    }

    /**
     * Set the value of [slug] column.
     * Human-readable mnemonic for the permission name
     * @param  string  $v  New value
     * @return $this The current object (for fluent API support)
     */
    public function setSlug($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->slug !== $v) {
            $this->slug                                          = $v;
            $this->modifiedColumns[PermissionTableMap::COL_SLUG] = true;
        }

        return $this;
    }

    /**
     * Set the value of [description] column.
     * Description of what the permission permits
     * @param  string  $v  New value
     * @return $this The current object (for fluent API support)
     */
    public function setDescription($v)
    {
        if ($v !== null) {
            $v = (string) $v;
        }

        if ($this->description !== $v) {
            $this->description                                          = $v;
            $this->modifiedColumns[PermissionTableMap::COL_DESCRIPTION] = true;
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
        if ($this->description !== '') {
            return false;
        }

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
     * @param  array  $row  The row returned by DataFetcher->fetch().
     * @param  int  $startcol  0-based offset column which indicates which resultset column to start with.
     * @param  bool  $rehydrate  Whether this object is being re-hydrated from the database.
     * @param  string  $indexType  The index type of $row. Mostly DataFetcher->getIndexType().
     * One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                            TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *
     * @return int next starting column
     * @throws \Propel\Runtime\Exception\PropelException - Any caught Exception will be rewrapped as a PropelException.
     */
    public function hydrate(
        array $row,
        int $startcol = 0,
        bool $rehydrate = false,
        string $indexType = TableMap::TYPE_NUM
    ): int {
        try {
            $col      = $row[TableMap::TYPE_NUM == $indexType ? 0 + $startcol : PermissionTableMap::translateFieldName(
                'Id',
                TableMap::TYPE_PHPNAME,
                $indexType
            )];
            $this->id = (null !== $col) ? (int)$col : null;

            $col        = $row[TableMap::TYPE_NUM == $indexType ? 1 + $startcol : PermissionTableMap::translateFieldName(
                'Slug',
                TableMap::TYPE_PHPNAME,
                $indexType
            )];
            $this->slug = (null !== $col) ? (string)$col : null;

            $col               = $row[TableMap::TYPE_NUM == $indexType ? 2 + $startcol : PermissionTableMap::translateFieldName(
                'Description',
                TableMap::TYPE_PHPNAME,
                $indexType
            )];
            $this->description = (null !== $col) ? (string)$col : null;

            $this->resetModified();
            $this->setNew(false);

            if ($rehydrate) {
                $this->ensureConsistency();
            }

            return $startcol + 3; // 3 = PermissionTableMap::NUM_HYDRATE_COLUMNS.

        } catch (Exception $e) {
            throw new PropelException(sprintf('Error populating %s object', '\\flapjack\\attend\\database\\Permission'), 0, $e);
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
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function ensureConsistency(): void
    {
    }

    /**
     * Reloads this object from datastore based on primary key and (optionally) resets all associated objects.
     *
     * This will only work if the object has been saved and has a valid primary key set.
     *
     * @param  bool  $deep  (optional) Whether to also de-associated any related objects.
     * @param  ConnectionInterface  $con  (optional) The ConnectionInterface connection to use.
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
            $con = Propel::getServiceContainer()->getReadConnection(PermissionTableMap::DATABASE_NAME);
        }

        // We don't need to alter the object instance pool; we're just modifying this instance
        // already in the pool.

        $dataFetcher = ChildPermissionQuery::create(null, $this->buildPkeyCriteria())->setFormatter(
            ModelCriteria::FORMAT_STATEMENT
        )->find($con);
        $row         = $dataFetcher->fetch();
        $dataFetcher->close();
        if ( ! $row) {
            throw new PropelException('Cannot find matching row in the database to reload object values.');
        }
        $this->hydrate($row, 0, true, $dataFetcher->getIndexType()); // rehydrate

        if ($deep) {  // also de-associate any related objects?

            $this->collGroupPermissions = null;

            $this->collIndividualPermissions = null;

        } // if (deep)
    }

    /**
     * Removes this object from datastore and sets delete attribute.
     *
     * @param  ConnectionInterface  $con
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     * @see Permission::setDeleted()
     * @see Permission::isDeleted()
     */
    public function delete(?ConnectionInterface $con = null): void
    {
        if ($this->isDeleted()) {
            throw new PropelException("This object has already been deleted.");
        }

        if ($con === null) {
            $con = Propel::getServiceContainer()->getWriteConnection(PermissionTableMap::DATABASE_NAME);
        }

        $con->transaction(function () use ($con) {
            $deleteQuery = ChildPermissionQuery::create()
                                               ->filterByPrimaryKey($this->getPrimaryKey());
            $ret         = $this->preDelete($con);
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
     * @param  ConnectionInterface  $con
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
            $con = Propel::getServiceContainer()->getWriteConnection(PermissionTableMap::DATABASE_NAME);
        }

        return $con->transaction(function () use ($con) {
            $ret      = $this->preSave($con);
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
                PermissionTableMap::addInstanceToPool($this);
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
     * @param  ConnectionInterface  $con
     * @return int The number of rows affected by this insert/update and any referring fk objects' save() operations.
     * @throws \Propel\Runtime\Exception\PropelException
     * @see save()
     */
    protected function doSave(ConnectionInterface $con): int
    {
        $affectedRows = 0; // initialize var to track total num of affected rows
        if ( ! $this->alreadyInSave) {
            $this->alreadyInSave = true;

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

            if ($this->groupPermissionsScheduledForDeletion !== null) {
                if ( ! $this->groupPermissionsScheduledForDeletion->isEmpty()) {
                    \flapjack\attend\database\GroupPermissionQuery::create()
                                                                  ->filterByPrimaryKeys(
                                                                      $this->groupPermissionsScheduledForDeletion->getPrimaryKeys(
                                                                          false
                                                                      )
                                                                  )
                                                                  ->delete($con);
                    $this->groupPermissionsScheduledForDeletion = null;
                }
            }

            if ($this->collGroupPermissions !== null) {
                foreach ($this->collGroupPermissions as $referrerFK) {
                    if ( ! $referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
                        $affectedRows += $referrerFK->save($con);
                    }
                }
            }

            if ($this->individualPermissionsScheduledForDeletion !== null) {
                if ( ! $this->individualPermissionsScheduledForDeletion->isEmpty()) {
                    \flapjack\attend\database\IndividualPermissionQuery::create()
                                                                       ->filterByPrimaryKeys(
                                                                           $this->individualPermissionsScheduledForDeletion->getPrimaryKeys(
                                                                               false
                                                                           )
                                                                       )
                                                                       ->delete($con);
                    $this->individualPermissionsScheduledForDeletion = null;
                }
            }

            if ($this->collIndividualPermissions !== null) {
                foreach ($this->collIndividualPermissions as $referrerFK) {
                    if ( ! $referrerFK->isDeleted() && ($referrerFK->isNew() || $referrerFK->isModified())) {
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
     * @param  ConnectionInterface  $con
     *
     * @throws \Propel\Runtime\Exception\PropelException
     * @see doSave()
     */
    protected function doInsert(ConnectionInterface $con): void
    {
        $modifiedColumns = [];
        $index = 0;

        $this->modifiedColumns[PermissionTableMap::COL_ID] = true;
        if (null !== $this->id) {
            throw new PropelException('Cannot insert a value for auto-increment primary key (' . PermissionTableMap::COL_ID . ')');
        }

        // check the columns in natural order for more readable SQL queries
        if ($this->isColumnModified(PermissionTableMap::COL_ID)) {
            $modifiedColumns[':p' . $index++]  = 'id';
        }
        if ($this->isColumnModified(PermissionTableMap::COL_SLUG)) {
            $modifiedColumns[':p' . $index++]  = 'slug';
        }
        if ($this->isColumnModified(PermissionTableMap::COL_DESCRIPTION)) {
            $modifiedColumns[':p' . $index++] = 'description';
        }

        $sql = sprintf(
            'INSERT INTO permissions (%s) VALUES (%s)',
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
                    case 'slug':
                        $stmt->bindValue($identifier, $this->slug, PDO::PARAM_STR);

                        break;
                    case 'description':
                        $stmt->bindValue($identifier, $this->description, PDO::PARAM_STR);

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
     * @param  ConnectionInterface  $con
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
     * @param  string  $name  name
     * @param  string  $type  The type of fieldname the $name is of:
     *                     one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                     TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                     Defaults to TableMap::TYPE_PHPNAME.
     * @return mixed Value of field.
     */
    public function getByName(string $name, string $type = TableMap::TYPE_PHPNAME)
    {
        $pos   = PermissionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);
        $field = $this->getByPosition($pos);

        return $field;
    }

    /**
     * Retrieves a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int  $pos  Position in XML schema
     * @return mixed Value of field at $pos
     */
    public function getByPosition(int $pos)
    {
        switch ($pos) {
            case 0:
                return $this->getId();

            case 1:
                return $this->getSlug();

            case 2:
                return $this->getDescription();

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
     * @param  string  $keyType  (optional) One of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME,
     *                    TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                    Defaults to TableMap::TYPE_PHPNAME.
     * @param  bool  $includeLazyLoadColumns  (optional) Whether to include lazy loaded columns. Defaults to TRUE.
     * @param  array  $alreadyDumpedObjects  List of objects to skip to avoid recursion
     * @param  bool  $includeForeignObjects  (optional) Whether to include hydrated related objects. Default to FALSE.
     *
     * @return array An associative array containing the field names (as keys) and field values
     */
    public function toArray(
        string $keyType = TableMap::TYPE_PHPNAME,
        bool $includeLazyLoadColumns = true,
        array $alreadyDumpedObjects = [],
        bool $includeForeignObjects = false
    ): array {
        if (isset($alreadyDumpedObjects['Permission'][$this->hashCode()])) {
            return ['*RECURSION*'];
        }
        $alreadyDumpedObjects['Permission'][$this->hashCode()] = true;
        $keys                                                  = PermissionTableMap::getFieldNames($keyType);
        $result                                                = [
            $keys[0] => $this->getId(),
            $keys[1] => $this->getSlug(),
            $keys[2] => $this->getDescription(),
        ];
        $virtualColumns                                        = $this->virtualColumns;
        foreach ($virtualColumns as $key => $virtualColumn) {
            $result[$key] = $virtualColumn;
        }

        if ($includeForeignObjects) {
            if (null !== $this->collGroupPermissions) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'groupPermissions';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'group_permissionss';
                        break;
                    default:
                        $key = 'GroupPermissions';
                }

                $result[$key] = $this->collGroupPermissions->toArray(
                    null,
                    false,
                    $keyType,
                    $includeLazyLoadColumns,
                    $alreadyDumpedObjects);
            }
            if (null !== $this->collIndividualPermissions) {

                switch ($keyType) {
                    case TableMap::TYPE_CAMELNAME:
                        $key = 'individualPermissions';
                        break;
                    case TableMap::TYPE_FIELDNAME:
                        $key = 'individual_permissionss';
                        break;
                    default:
                        $key = 'IndividualPermissions';
                }

                $result[$key] = $this->collIndividualPermissions->toArray(
                    null,
                    false,
                    $keyType,
                    $includeLazyLoadColumns,
                    $alreadyDumpedObjects);
            }
        }

        return $result;
    }

    /**
     * Sets a field from the object by name passed in as a string.
     *
     * @param  string  $name
     * @param  mixed  $value  field value
     * @param  string  $type  The type of fieldname the $name is of:
     *                one of the class type constants TableMap::TYPE_PHPNAME, TableMap::TYPE_CAMELNAME
     *                TableMap::TYPE_COLNAME, TableMap::TYPE_FIELDNAME, TableMap::TYPE_NUM.
     *                Defaults to TableMap::TYPE_PHPNAME.
     * @return $this
     */
    public function setByName(string $name, $value, string $type = TableMap::TYPE_PHPNAME)
    {
        $pos = PermissionTableMap::translateFieldName($name, $type, TableMap::TYPE_NUM);

        $this->setByPosition($pos, $value);

        return $this;
    }

    /**
     * Sets a field from the object by Position as specified in the xml schema.
     * Zero-based.
     *
     * @param  int  $pos  position in xml schema
     * @param  mixed  $value  field value
     * @return $this
     */
    public function setByPosition(int $pos, $value)
    {
        switch ($pos) {
            case 0:
                $this->setId($value);
                break;
            case 1:
                $this->setSlug($value);
                break;
            case 2:
                $this->setDescription($value);
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
     * @param  array  $arr  An array to populate the object from.
     * @param  string  $keyType  The type of keys the array uses.
     *
     * @return $this
     */
    public function fromArray(array $arr, string $keyType = TableMap::TYPE_PHPNAME)
    {
        $keys = PermissionTableMap::getFieldNames($keyType);

        if (array_key_exists($keys[0], $arr)) {
            $this->setId($arr[$keys[0]]);
        }
        if (array_key_exists($keys[1], $arr)) {
            $this->setSlug($arr[$keys[1]]);
        }
        if (array_key_exists($keys[2], $arr)) {
            $this->setDescription($arr[$keys[2]]);
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
     * @param  mixed  $parser  A AbstractParser instance,
     *                       or a format name ('XML', 'YAML', 'JSON', 'CSV')
     * @param  string  $data  The source data to import from
     * @param  string  $keyType  The type of keys the array uses.
     *
     * @return $this The current object, for fluid interface
     */
    public function importFrom($parser, string $data, string $keyType = TableMap::TYPE_PHPNAME)
    {
        if ( ! $parser instanceof AbstractParser) {
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
        $criteria = new Criteria(PermissionTableMap::DATABASE_NAME);

        if ($this->isColumnModified(PermissionTableMap::COL_ID)) {
            $criteria->add(PermissionTableMap::COL_ID, $this->id);
        }
        if ($this->isColumnModified(PermissionTableMap::COL_SLUG)) {
            $criteria->add(PermissionTableMap::COL_SLUG, $this->slug);
        }
        if ($this->isColumnModified(PermissionTableMap::COL_DESCRIPTION)) {
            $criteria->add(PermissionTableMap::COL_DESCRIPTION, $this->description);
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
        $criteria = ChildPermissionQuery::create();
        $criteria->add(PermissionTableMap::COL_ID, $this->id);

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
     * @param  int|null  $key  Primary key.
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
     * @param  object  $copyObj  An object of \flapjack\attend\database\Permission (or compatible) type.
     * @param  bool  $deepCopy  Whether to also copy all rows that refer (by fkey) to the current row.
     * @param  bool  $makeNew  Whether to reset autoincrement PKs and make the object new.
     * @throws \Propel\Runtime\Exception\PropelException
     * @return void
     */
    public function copyInto(object $copyObj, bool $deepCopy = false, bool $makeNew = true): void
    {
        $copyObj->setSlug($this->getSlug());
        $copyObj->setDescription($this->getDescription());

        if ($deepCopy) {
            // important: temporarily setNew(false) because this affects the behavior of
            // the getter/setter methods for fkey referrer objects.
            $copyObj->setNew(false);

            foreach ($this->getGroupPermissions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addGroupPermission($relObj->copy($deepCopy));
                }
            }

            foreach ($this->getIndividualPermissions() as $relObj) {
                if ($relObj !== $this) {  // ensure that we don't try to copy a reference to ourselves
                    $copyObj->addIndividualPermission($relObj->copy($deepCopy));
                }
            }

        } // if ($deepCopy)

        if ($makeNew) {
            $copyObj->setNew(true);
            $copyObj->setId(null); // this is a auto-increment column, so set to default value
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
     * @param  bool  $deepCopy  Whether to also copy all rows that refer (by fkey) to the current row.
     * @return \flapjack\attend\database\Permission Clone of current object.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function copy(bool $deepCopy = false)
    {
        // we use get_class(), because this might be a subclass
        $clazz   = get_class($this);
        $copyObj = new $clazz();
        $this->copyInto($copyObj, $deepCopy);

        return $copyObj;
    }


    /**
     * Initializes a collection based on the name of a relation.
     * Avoids crafting an 'init[$relationName]s' method name
     * that wouldn't work when StandardEnglishPluralizer is used.
     *
     * @param  string  $relationName  The name of the relation to initialize
     * @return void
     */
    public function initRelation($relationName): void
    {
        if ('GroupPermission' === $relationName) {
            $this->initGroupPermissions();
            return;
        }
        if ('IndividualPermission' === $relationName) {
            $this->initIndividualPermissions();
            return;
        }
    }

    /**
     * Clears out the collGroupPermissions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return $this
     * @see addGroupPermissions()
     */
    public function clearGroupPermissions()
    {
        $this->collGroupPermissions = null; // important to set this to NULL since that means it is uninitialized

        return $this;
    }

    /**
     * Reset is the collGroupPermissions collection loaded partially.
     *
     * @return void
     */
    public function resetPartialGroupPermissions($v = true): void
    {
        $this->collGroupPermissionsPartial = $v;
    }

    /**
     * Initializes the collGroupPermissions collection.
     *
     * By default this just sets the collGroupPermissions collection to an empty array (like clearcollGroupPermissions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param  bool  $overrideExisting  If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initGroupPermissions(bool $overrideExisting = true): void
    {
        if (null !== $this->collGroupPermissions && ! $overrideExisting) {
            return;
        }

        $collectionClassName = GroupPermissionTableMap::getTableMap()->getCollectionClassName();

        $this->collGroupPermissions = new $collectionClassName;
        $this->collGroupPermissions->setModel('\flapjack\attend\database\GroupPermission');
    }

    /**
     * Gets an array of ChildGroupPermission objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPermission is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param  Criteria  $criteria  optional Criteria object to narrow the query
     * @param  ConnectionInterface  $con  optional connection object
     * @return ObjectCollection|ChildGroupPermission[] List of ChildGroupPermission objects
     * @phpstan-return ObjectCollection&\Traversable<ChildGroupPermission> List of ChildGroupPermission objects
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getGroupPermissions(?Criteria $criteria = null, ?ConnectionInterface $con = null)
    {
        $partial = $this->collGroupPermissionsPartial && !$this->isNew();
        if (null === $this->collGroupPermissions || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collGroupPermissions) {
                    $this->initGroupPermissions();
                } else {
                    $collectionClassName = GroupPermissionTableMap::getTableMap()->getCollectionClassName();

                    $collGroupPermissions = new $collectionClassName;
                    $collGroupPermissions->setModel('\flapjack\attend\database\GroupPermission');

                    return $collGroupPermissions;
                }
            } else {
                $collGroupPermissions = ChildGroupPermissionQuery::create(null, $criteria)
                                                                 ->filterByPermission($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collGroupPermissionsPartial && count($collGroupPermissions)) {
                        $this->initGroupPermissions(false);

                        foreach ($collGroupPermissions as $obj) {
                            if (false == $this->collGroupPermissions->contains($obj)) {
                                $this->collGroupPermissions->append($obj);
                            }
                        }

                        $this->collGroupPermissionsPartial = true;
                    }

                    return $collGroupPermissions;
                }

                if ($partial && $this->collGroupPermissions) {
                    foreach ($this->collGroupPermissions as $obj) {
                        if ($obj->isNew()) {
                            $collGroupPermissions[] = $obj;
                        }
                    }
                }

                $this->collGroupPermissions        = $collGroupPermissions;
                $this->collGroupPermissionsPartial = false;
            }
        }

        return $this->collGroupPermissions;
    }

    /**
     * Sets a collection of ChildGroupPermission objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection  $groupPermissions  A Propel collection.
     * @param  ConnectionInterface  $con  Optional connection object
     * @return $this The current object (for fluent API support)
     */
    public function setGroupPermissions(Collection $groupPermissions, ?ConnectionInterface $con = null)
    {
        /** @var ChildGroupPermission[] $groupPermissionsToDelete */
        $groupPermissionsToDelete = $this->getGroupPermissions(new Criteria(), $con)->diff($groupPermissions);


        $this->groupPermissionsScheduledForDeletion = $groupPermissionsToDelete;

        foreach ($groupPermissionsToDelete as $groupPermissionRemoved) {
            $groupPermissionRemoved->setPermission(null);
        }

        $this->collGroupPermissions = null;
        foreach ($groupPermissions as $groupPermission) {
            $this->addGroupPermission($groupPermission);
        }

        $this->collGroupPermissions        = $groupPermissions;
        $this->collGroupPermissionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related GroupPermission objects.
     *
     * @param  Criteria  $criteria
     * @param  bool  $distinct
     * @param  ConnectionInterface  $con
     *
     * @return int Count of related GroupPermission objects.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function countGroupPermissions(
        ?Criteria $criteria = null,
        bool $distinct = false,
        ?ConnectionInterface $con = null
    ): int {
        $partial = $this->collGroupPermissionsPartial && ! $this->isNew();
        if (null === $this->collGroupPermissions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collGroupPermissions) {
                return 0;
            }

            if ($partial && ! $criteria) {
                return count($this->getGroupPermissions());
            }

            $query = ChildGroupPermissionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPermission($this)
                ->count($con);
        }

        return count($this->collGroupPermissions);
    }

    /**
     * Method called to associate a ChildGroupPermission object to this object
     * through the ChildGroupPermission foreign key attribute.
     *
     * @param  ChildGroupPermission  $l  ChildGroupPermission
     * @return $this The current object (for fluent API support)
     */
    public function addGroupPermission(ChildGroupPermission $l)
    {
        if ($this->collGroupPermissions === null) {
            $this->initGroupPermissions();
            $this->collGroupPermissionsPartial = true;
        }

        if ( ! $this->collGroupPermissions->contains($l)) {
            $this->doAddGroupPermission($l);

            if ($this->groupPermissionsScheduledForDeletion and $this->groupPermissionsScheduledForDeletion->contains(
                    $l
                )) {
                $this->groupPermissionsScheduledForDeletion->remove(
                    $this->groupPermissionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  ChildGroupPermission  $groupPermission  The ChildGroupPermission object to add.
     */
    protected function doAddGroupPermission(ChildGroupPermission $groupPermission): void
    {
        $this->collGroupPermissions[] = $groupPermission;
        $groupPermission->setPermission($this);
    }

    /**
     * @param  ChildGroupPermission  $groupPermission  The ChildGroupPermission object to remove.
     * @return $this The current object (for fluent API support)
     */
    public function removeGroupPermission(ChildGroupPermission $groupPermission)
    {
        if ($this->getGroupPermissions()->contains($groupPermission)) {
            $pos = $this->collGroupPermissions->search($groupPermission);
            $this->collGroupPermissions->remove($pos);
            if (null === $this->groupPermissionsScheduledForDeletion) {
                $this->groupPermissionsScheduledForDeletion = clone $this->collGroupPermissions;
                $this->groupPermissionsScheduledForDeletion->clear();
            }
            $this->groupPermissionsScheduledForDeletion[] = clone $groupPermission;
            $groupPermission->setPermission(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Permission is new, it will return
     * an empty collection; or if this Permission has previously
     * been saved, it will retrieve related GroupPermissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Permission.
     *
     * @param  Criteria  $criteria  optional Criteria object to narrow the query
     * @param  ConnectionInterface  $con  optional connection object
     * @param  string  $joinBehavior  optional join type to use (defaults to Criteria::LEFT_JOIN)
     *
     * @return ObjectCollection|ChildGroupPermission[] List of ChildGroupPermission objects
     * @phpstan-return ObjectCollection&\Traversable<ChildGroupPermission}> List of ChildGroupPermission objects
     */
    public function getGroupPermissionsJoinGroup(
        ?Criteria $criteria = null,
        ?ConnectionInterface $con = null,
        $joinBehavior = Criteria::LEFT_JOIN
    ) {
        $query = ChildGroupPermissionQuery::create(null, $criteria);
        $query->joinWith('Group', $joinBehavior);

        return $this->getGroupPermissions($query, $con);
    }

    /**
     * Clears out the collIndividualPermissions collection
     *
     * This does not modify the database; however, it will remove any associated objects, causing
     * them to be refetched by subsequent calls to accessor method.
     *
     * @return $this
     * @see addIndividualPermissions()
     */
    public function clearIndividualPermissions()
    {
        $this->collIndividualPermissions = null; // important to set this to NULL since that means it is uninitialized

        return $this;
    }

    /**
     * Reset is the collIndividualPermissions collection loaded partially.
     *
     * @return void
     */
    public function resetPartialIndividualPermissions($v = true): void
    {
        $this->collIndividualPermissionsPartial = $v;
    }

    /**
     * Initializes the collIndividualPermissions collection.
     *
     * By default this just sets the collIndividualPermissions collection to an empty array (like clearcollIndividualPermissions());
     * however, you may wish to override this method in your stub class to provide setting appropriate
     * to your application -- for example, setting the initial array to the values stored in database.
     *
     * @param  bool  $overrideExisting  If set to true, the method call initializes
     *                                        the collection even if it is not empty
     *
     * @return void
     */
    public function initIndividualPermissions(bool $overrideExisting = true): void
    {
        if (null !== $this->collIndividualPermissions && ! $overrideExisting) {
            return;
        }

        $collectionClassName = IndividualPermissionTableMap::getTableMap()->getCollectionClassName();

        $this->collIndividualPermissions = new $collectionClassName;
        $this->collIndividualPermissions->setModel('\flapjack\attend\database\IndividualPermission');
    }

    /**
     * Gets an array of ChildIndividualPermission objects which contain a foreign key that references this object.
     *
     * If the $criteria is not null, it is used to always fetch the results from the database.
     * Otherwise the results are fetched from the database the first time, then cached.
     * Next time the same method is called without $criteria, the cached collection is returned.
     * If this ChildPermission is new, it will return
     * an empty collection or the current collection; the criteria is ignored on a new object.
     *
     * @param  Criteria  $criteria  optional Criteria object to narrow the query
     * @param  ConnectionInterface  $con  optional connection object
     * @return ObjectCollection|ChildIndividualPermission[] List of ChildIndividualPermission objects
     * @phpstan-return ObjectCollection&\Traversable<ChildIndividualPermission> List of ChildIndividualPermission objects
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getIndividualPermissions(?Criteria $criteria = null, ?ConnectionInterface $con = null)
    {
        $partial = $this->collIndividualPermissionsPartial && !$this->isNew();
        if (null === $this->collIndividualPermissions || null !== $criteria || $partial) {
            if ($this->isNew()) {
                // return empty collection
                if (null === $this->collIndividualPermissions) {
                    $this->initIndividualPermissions();
                } else {
                    $collectionClassName = IndividualPermissionTableMap::getTableMap()->getCollectionClassName();

                    $collIndividualPermissions = new $collectionClassName;
                    $collIndividualPermissions->setModel('\flapjack\attend\database\IndividualPermission');

                    return $collIndividualPermissions;
                }
            } else {
                $collIndividualPermissions = ChildIndividualPermissionQuery::create(null, $criteria)
                    ->filterByPermission($this)
                    ->find($con);

                if (null !== $criteria) {
                    if (false !== $this->collIndividualPermissionsPartial && count($collIndividualPermissions)) {
                        $this->initIndividualPermissions(false);

                        foreach ($collIndividualPermissions as $obj) {
                            if (false == $this->collIndividualPermissions->contains($obj)) {
                                $this->collIndividualPermissions->append($obj);
                            }
                        }

                        $this->collIndividualPermissionsPartial = true;
                    }

                    return $collIndividualPermissions;
                }

                if ($partial && $this->collIndividualPermissions) {
                    foreach ($this->collIndividualPermissions as $obj) {
                        if ($obj->isNew()) {
                            $collIndividualPermissions[] = $obj;
                        }
                    }
                }

                $this->collIndividualPermissions        = $collIndividualPermissions;
                $this->collIndividualPermissionsPartial = false;
            }
        }

        return $this->collIndividualPermissions;
    }

    /**
     * Sets a collection of ChildIndividualPermission objects related by a one-to-many relationship
     * to the current object.
     * It will also schedule objects for deletion based on a diff between old objects (aka persisted)
     * and new objects from the given Propel collection.
     *
     * @param  Collection  $individualPermissions  A Propel collection.
     * @param  ConnectionInterface  $con  Optional connection object
     * @return $this The current object (for fluent API support)
     */
    public function setIndividualPermissions(Collection $individualPermissions, ?ConnectionInterface $con = null)
    {
        /** @var ChildIndividualPermission[] $individualPermissionsToDelete */
        $individualPermissionsToDelete = $this->getIndividualPermissions(new Criteria(), $con)->diff($individualPermissions);


        $this->individualPermissionsScheduledForDeletion = $individualPermissionsToDelete;

        foreach ($individualPermissionsToDelete as $individualPermissionRemoved) {
            $individualPermissionRemoved->setPermission(null);
        }

        $this->collIndividualPermissions = null;
        foreach ($individualPermissions as $individualPermission) {
            $this->addIndividualPermission($individualPermission);
        }

        $this->collIndividualPermissions        = $individualPermissions;
        $this->collIndividualPermissionsPartial = false;

        return $this;
    }

    /**
     * Returns the number of related IndividualPermission objects.
     *
     * @param  Criteria  $criteria
     * @param  bool  $distinct
     * @param  ConnectionInterface  $con
     *
     * @return int Count of related IndividualPermission objects.
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function countIndividualPermissions(
        ?Criteria $criteria = null,
        bool $distinct = false,
        ?ConnectionInterface $con = null
    ): int {
        $partial = $this->collIndividualPermissionsPartial && ! $this->isNew();
        if (null === $this->collIndividualPermissions || null !== $criteria || $partial) {
            if ($this->isNew() && null === $this->collIndividualPermissions) {
                return 0;
            }

            if ($partial && ! $criteria) {
                return count($this->getIndividualPermissions());
            }

            $query = ChildIndividualPermissionQuery::create(null, $criteria);
            if ($distinct) {
                $query->distinct();
            }

            return $query
                ->filterByPermission($this)
                ->count($con);
        }

        return count($this->collIndividualPermissions);
    }

    /**
     * Method called to associate a ChildIndividualPermission object to this object
     * through the ChildIndividualPermission foreign key attribute.
     *
     * @param  ChildIndividualPermission  $l  ChildIndividualPermission
     * @return $this The current object (for fluent API support)
     */
    public function addIndividualPermission(ChildIndividualPermission $l)
    {
        if ($this->collIndividualPermissions === null) {
            $this->initIndividualPermissions();
            $this->collIndividualPermissionsPartial = true;
        }

        if ( ! $this->collIndividualPermissions->contains($l)) {
            $this->doAddIndividualPermission($l);

            if ($this->individualPermissionsScheduledForDeletion and $this->individualPermissionsScheduledForDeletion->contains(
                    $l
                )) {
                $this->individualPermissionsScheduledForDeletion->remove(
                    $this->individualPermissionsScheduledForDeletion->search($l));
            }
        }

        return $this;
    }

    /**
     * @param  ChildIndividualPermission  $individualPermission  The ChildIndividualPermission object to add.
     */
    protected function doAddIndividualPermission(ChildIndividualPermission $individualPermission): void
    {
        $this->collIndividualPermissions[] = $individualPermission;
        $individualPermission->setPermission($this);
    }

    /**
     * @param  ChildIndividualPermission  $individualPermission  The ChildIndividualPermission object to remove.
     * @return $this The current object (for fluent API support)
     */
    public function removeIndividualPermission(ChildIndividualPermission $individualPermission)
    {
        if ($this->getIndividualPermissions()->contains($individualPermission)) {
            $pos = $this->collIndividualPermissions->search($individualPermission);
            $this->collIndividualPermissions->remove($pos);
            if (null === $this->individualPermissionsScheduledForDeletion) {
                $this->individualPermissionsScheduledForDeletion = clone $this->collIndividualPermissions;
                $this->individualPermissionsScheduledForDeletion->clear();
            }
            $this->individualPermissionsScheduledForDeletion[] = clone $individualPermission;
            $individualPermission->setPermission(null);
        }

        return $this;
    }


    /**
     * If this collection has already been initialized with
     * an identical criteria, it returns the collection.
     * Otherwise if this Permission is new, it will return
     * an empty collection; or if this Permission has previously
     * been saved, it will retrieve related IndividualPermissions from storage.
     *
     * This method is protected by default in order to keep the public
     * api reasonable.  You can provide public methods for those you
     * actually need in Permission.
     *
     * @param  Criteria  $criteria  optional Criteria object to narrow the query
     * @param  ConnectionInterface  $con  optional connection object
     * @param  string  $joinBehavior  optional join type to use (defaults to Criteria::LEFT_JOIN)
     *
     * @return ObjectCollection|ChildIndividualPermission[] List of ChildIndividualPermission objects
     * @phpstan-return ObjectCollection&\Traversable<ChildIndividualPermission}> List of ChildIndividualPermission objects
     */
    public function getIndividualPermissionsJoinAccount(
        ?Criteria $criteria = null,
        ?ConnectionInterface $con = null,
        $joinBehavior = Criteria::LEFT_JOIN
    ) {
        $query = ChildIndividualPermissionQuery::create(null, $criteria);
        $query->joinWith('Account', $joinBehavior);

        return $this->getIndividualPermissions($query, $con);
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
        $this->id            = null;
        $this->slug          = null;
        $this->description   = null;
        $this->alreadyInSave = false;
        $this->clearAllReferences();
        $this->applyDefaultValues();
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
     * @param  bool  $deep  Whether to also clear the references on all referrer objects.
     * @return $this
     */
    public function clearAllReferences(bool $deep = false)
    {
        if ($deep) {
            if ($this->collGroupPermissions) {
                foreach ($this->collGroupPermissions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
            if ($this->collIndividualPermissions) {
                foreach ($this->collIndividualPermissions as $o) {
                    $o->clearAllReferences($deep);
                }
            }
        } // if ($deep)

        $this->collGroupPermissions      = null;
        $this->collIndividualPermissions = null;
        return $this;
    }

    /**
     * Return the string representation of this object
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->exportTo(PermissionTableMap::DEFAULT_STRING_FORMAT);
    }

    /**
     * Code to be run before persisting the object
     * @param  ConnectionInterface|null $con
     * @return bool
     */
    public function preSave(?ConnectionInterface $con = null): bool
    {
        return true;
    }

    /**
     * Code to be run after persisting the object
     * @param  ConnectionInterface|null $con
     * @return void
     */
    public function postSave(?ConnectionInterface $con = null): void
    {
    }

    /**
     * Code to be run before inserting to database
     * @param  ConnectionInterface|null $con
     * @return bool
     */
    public function preInsert(?ConnectionInterface $con = null): bool
    {
        return true;
    }

    /**
     * Code to be run after inserting to database
     * @param  ConnectionInterface|null $con
     * @return void
     */
    public function postInsert(?ConnectionInterface $con = null): void
    {
    }

    /**
     * Code to be run before updating the object in database
     * @param  ConnectionInterface|null $con
     * @return bool
     */
    public function preUpdate(?ConnectionInterface $con = null): bool
    {
        return true;
    }

    /**
     * Code to be run after updating the object in database
     * @param  ConnectionInterface|null $con
     * @return void
     */
    public function postUpdate(?ConnectionInterface $con = null): void
    {
    }

    /**
     * Code to be run before deleting the object in database
     * @param  ConnectionInterface|null $con
     * @return bool
     */
    public function preDelete(?ConnectionInterface $con = null): bool
    {
        return true;
    }

    /**
     * Code to be run after deleting the object in database
     * @param  ConnectionInterface|null $con
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
     * @param  string  $name
     * @param  mixed  $params
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
            $format    = substr($name, 4);
            $inputData = $params[0];
            $keyType = $params[1] ?? TableMap::TYPE_PHPNAME;

            return $this->importFrom($format, $inputData, $keyType);
        }

        if (0 === strpos($name, 'to')) {
            $format                 = substr($name, 2);
            $includeLazyLoadColumns = $params[0] ?? true;
            $keyType = $params[1] ?? TableMap::TYPE_PHPNAME;

            return $this->exportTo($format, $includeLazyLoadColumns, $keyType);
        }

        throw new BadMethodCallException(sprintf('Call to undefined method: %s.', $name));
    }

}
