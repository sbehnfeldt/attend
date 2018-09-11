<?php

namespace Attend;

use PDO;
use Psr\Container\ContainerInterface as Container;


class Repository
{
    /** @var Container $container */
    private $container;

    /** @var  PDO $pdo */
    private $pdo;


    public function __construct(Container $container, PDO $pdo = null)
    {
        $this->container = $container;
        $this->pdo       = $pdo;
    }

    /**
     * @return Container
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     */
    public function setContainer(Container $container)
    {
        $this->container = $container;
        $this->pdo       = null;
    }


    /**
     * @return PDO
     */
    public function getPdo()
    {
        if (null == $this->pdo) {
            $this->pdo = $this->container[ 'pdo' ];
        }

        return $this->pdo;
    }

    /**
     * @param PDO $pdo
     */
    public function setPdo(PDO $pdo)
    {
        $this->pdo = $pdo;
    }


    public function select()
    {
        $sql     = 'SELECT * FROM classrooms';
        $sth     = $this->getPdo()->prepare($sql);
        $b       = $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }

    public function insert($parsedBody)
    {
        $sql = 'INSERT INTO classrooms(label) VALUES(?)';
        $sth = $this->getPdo()->prepare($sql);
        $b   = $sth->execute([$parsedBody[ 'label' ]]);

        $id = $this->getPdo()->lastInsertId();

        return $id;
    }

    public function updateOne($id, $updates)
    {
        $sql = 'UPDATE classrooms SET label=? WHERE id=?';
        $sth = $this->getPdo()->prepare($sql);
        $b   = $sth->execute([$updates[ 'label' ], $id]);

        return;
    }

    public function deleteOne($id)
    {
        $sql = 'DELETE FROM classrooms WHERE id=?';
        $sth = $this->getPdo()->prepare($sql);
        $b   = $sth->execute([$id]);

        return;
    }
}
