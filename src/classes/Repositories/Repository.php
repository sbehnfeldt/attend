<?php

namespace Repositories;

use PDO;

abstract class Repository {

	/** @var  PDO */
	private $pdo;


	public function __construct( $pdo ) {
		$this->pdo = $pdo;
	}

	/**
	 * @return PDO
	 */
	protected function getPdo() {
		return $this->pdo;
	}

	/**
	 * @param PDO $pdo
	 */
	protected function setPdo( $pdo ) {
		$this->pdo = $pdo;
	}


	abstract protected function getTableName();

	abstract protected function getColumnsNames();

	protected function translateTableRow( $row ) {
		return $row;
	}

	public function select() {
		$resources = [ ];
		$sql       = 'select ' . implode( ', ', $this->getColumnsNames() ) . ' from ' . $this->getTableName();
		$rows      = $this->getPdo()->query( $sql );
		foreach ( $rows as $row ) {
			$resources[] = $this->translateTableRow( $row );
		}

		return $resources;
	}

}