<?php

use PDO;

class ClassroomRepo {

	/** @var  PDO */
	private $pdo;

	public function __construct( $pdo ) {
		$this->pdo = $pdo;
	}


	public function select() {
		$classrooms = [ ];
		$rows       = $this->pdo->query( 'select * from classrooms' );
		foreach ( $rows as $row ) {
			$classrooms[] = [
				'id'   => $row['id'],
				'name' => $row['name']
			];
		}

		return $classrooms;
	}


	public function selectOne( $id ) {
		$classrooms = [ ];
		$sth        = $this->pdo->prepare( 'select * from classrooms where id=:id' );
		$rows       = $sth->execute( [ ':id' => $id ] );
		while ( $row = $sth->fetch() ) {
			$classrooms[] = [
				'id'   => $row['id'],
				'name' => $row['name']
			];
		}

		return $classrooms;
	}
}
