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



}