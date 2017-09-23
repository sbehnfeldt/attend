<?php
namespace Repositories;

use PDO;


class ClassroomRepo {

	/** @var  PDO */
	private $pdo;

	public function __construct( $pdo ) {
		$this->pdo = $pdo;
	}


	public function insert( $post ) {
		$sql = 'insert into classrooms (name) values (:name)';
		$sth = $this->pdo->prepare( $sql );
		$sth->execute( [ ':name' => $post['name'] ] );
		$id = $this->pdo->lastInsertId();

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

	public function update( $id, $params ) {
		$cols = [ ];
		$vals = [ ];
		foreach ( $params as $k => $v ) {
			$cols[] = "$k = ?";
			$vals[] = $v;
		}
		$cols   = implode( ', ', $cols );
		$sql    = "update classrooms set $cols where id = ?";
		$vals[] = $id;
		$sth    = $this->pdo->prepare( $sql );
		$bool   = $sth->execute( $vals );

		return $id;
	}

	public function remove( $id ) {
		$sth = $this->pdo->prepare( 'delete from classrooms where id=:id' );
		$sth->execute( [ ':id' => $id ] );

		return $id;
	}
}
