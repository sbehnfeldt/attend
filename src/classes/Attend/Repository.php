<?php

namespace Attend;

use Psr\Container\ContainerInterface as Container;


class Repository
{
    /** @var Container $container */
    private $container;

    /** @var  PDO $pdo */
    private $pdo;


    public function __construct(Container $container, $pdo = null)
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
    public function setContainer($container)
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
    public function setPdo($pdo)
    {
        $this->pdo = $pdo;
    }

    public function select()
    {
        $pdo     = $this->container->get('pdo');
        $sql     = 'SELECT * FROM classrooms';
        $sth     = $pdo->prepare($sql);
        $b       = $sth->execute();
        $results = $sth->fetchAll();

        return $results;
    }
}
