<?php
namespace Repositories;


class ClassroomRepo extends Repository {

	public function insert( $post ) {
		$sql = 'insert into classrooms (name) values (:name)';
		$sth = $this->getPdo()->prepare( $sql );
		$sth->execute( [ ':name' => $post['name'] ] );
		$id = $this->getPdo()->lastInsertId();

		return $id;
	}

	public function select() {
		$classrooms = [ ];
		$rows       = $this->getPdo()->query( 'select * from classrooms' );
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
		$sth        = $this->getPdo()->prepare( 'select * from classrooms where id=:id' );
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
		$sth    = $this->getPdo()->prepare( $sql );
		$bool   = $sth->execute( $vals );

		return $id;
	}

	public function remove( $id ) {
		$sth = $this->getPdo()->prepare( 'delete from classrooms where id=:id' );
		$sth->execute( [ ':id' => $id ] );

		return $id;
	}
}
