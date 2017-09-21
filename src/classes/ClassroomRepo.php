<?php

use PDO;

class ClassroomRepo {

	/** @var  PDO */
	private $pdo;

	public function __construct( $pdo ) {
		$this->pdo = $pdo;
	}


	public function insert( $post ) {
		global $config;
		$host   = $config['db']['host'];
		$dbname = $config['db']['dbname'];
		$uname  = $config['db']['uname'];
		$pword  = $config['db']['pword'];
		$pdo    = new \PDO( "mysql:host=$host;dbname=$dbname", $uname, $pword );

		$sql = 'insert into classrooms (name) values (:name)';
		$sth = $pdo->prepare( $sql );
//		$pdo->beginTransaction();
		$bool = $sth->execute( [':name' => $post['name']]);
		$id = $pdo->lastInsertId();
//		$this->pdo->commit();
		return $id;
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
