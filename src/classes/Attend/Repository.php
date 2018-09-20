<?php

namespace Attend;

use Exception;
use PDO;


abstract class Repository implements iRepository
{
    /** @var  PDO $pdo */
    private $pdo;


    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @param PDO $pdo
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @return PDO
     */
    public function getPdo()
    {
        return $this->pdo;
    }

    public function getPrimaryKey()
    {
        return 'id';
    }

    public function getColumns($op)
    {
        $columns = [];
        foreach (static::getColumnNames() as $k => $v) {
            if ($v[ $op ]) {
                $columns[] = $k;
            }
        }

        return $columns;
    }

    public function select()
    {
        $sql     = sprintf("SELECT %s FROM %s",
            implode(', ', $this->getColumns('select')),
            $this->getTableName());
        $sth     = $this->pdo->prepare($sql);
        $b       = $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function selectOne($id)
    {
        $sql     = sprintf("SELECT %s FROM %s where %s = ?",
            implode(', ', $this->getColumns('select')),
            $this->getTableName(),
            $this->getPrimaryKey());
        $sth     = $this->pdo->prepare($sql);
        $b       = $sth->execute([$id]);
        $results = $sth->fetchAll();

        return $results;
    }

    public function insert($parsedBody)
    {
        $allCols = static::getColumnNames();
        $cols    = [];
        $vals    = [];

        foreach ($parsedBody as $k => $v) {
            if ( ! isset($allCols[ $k ])) {
                throw new Exception(sprintf('Unknown column name "%s"'), $k);
            }
            if ( ! isset($allCols[ $k ][ 'insert' ])) {
                throw new Exception(sprintf('Insert rule not defined for column "%s"'), $k);
            }
            if (is_callable($allCols[ $k ][ 'insert' ])) {
                if ( ! $allCols[ $k ][ 'insert' ]()) {
                    throw new Exception(sprintf('Cannot write to column "%s"', $k));
                }
            } else if ( ! $allCols[ $k ][ 'insert' ]) {
                throw new Exception(sprintf('Cannot write to column "%s"', $k));
            }
            $cols[] = $k;
            $vals[] = $v;
        }

        $sql = sprintf("INSERT INTO %s (%s) VALUES(%s)",
            $this->getTableName(),
            implode(', ', $cols),
            implode(', ', array_fill(0, count($cols), '?'))
        );
        $sth = $this->pdo->prepare($sql);
        $b   = $sth->execute($vals);

        $id = $this->pdo->lastInsertId();

        return $id;
    }


// Break the update data associative arrays into 2 parallel indexed arrays
    static protected function preProcessUpdates($updates)
    {
        $params = $values = [];
        foreach ($updates as $k => $v) {
            $params[] = $k;
            $values[] = $v;
        }

        return [$params, $values];
    }


    public function updateOne($id, $updates)
    {
        list($params, $values) = static::preProcessUpdates($updates);
        $values[] = $id;
        for ($i = 0; $i < count($params); $i++) {
            $params[ $i ] .= '= ?';
        }
        $params = implode(', ', $params);
        $sql    = sprintf("UPDATE %s SET %s WHERE id=?", $this->getTableName(), $params);
        $sth    = $this->pdo->prepare($sql);
        $b      = $sth->execute($values);

        return;
    }


    public function deleteOne($id)
    {
        $sql = sprintf("DELETE FROM %s WHERE id=?", $this->getTableName());
        $sth = $this->pdo->prepare($sql);
        $b   = $sth->execute([$id]);

        return;
    }
}
