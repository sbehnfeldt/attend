<?php
namespace Repositories;


class StudentRepo extends Repository {

	public function select() {
		$students = [ ];
		$rows     = $this->getPdo()->query( 'select id, family_name, first_name, enrolled, classroom_id from students' );
		foreach ( $rows as $row ) {
			$students[] = [
				'id'          => $row['id'],
				'familyName'  => $row['family_name'],
				'firstName'   => $row['first_name'],
				'enrolled'    => $row['enrolled'] ? true : false,
				'classroomId' => $row['classroom_id']
			];
		}

		return $students;
	}

	public function selectOne( $id ) {
		$students = [ ];
		$sth      = $this->getPdo()->prepare( 'select id, family_name, first_name, enrolled, classroom_id from students where id = :id' );
		$rows     = $sth->execute( [ ':id' => $id ] );

		while ( $row = $sth->fetch() ) {
			$students[] = [
				'id'          => $row['id'],
				'familyName'  => $row['family_name'],
				'firstName'   => $row['first_name'],
				'enrolled'    => $row['enrolled'] ? true : false,
				'classroomId' => $row['classroom_id']
			];
		}

		return $students;
	}

	public function remove( $id ) {
		$sth = $this->getPdo()->prepare( 'delete from students where id=:id' );
		$sth->execute( [ ':id' => $id ] );

		return $id;
	}
}