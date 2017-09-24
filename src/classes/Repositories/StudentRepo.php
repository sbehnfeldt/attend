<?php
namespace Repositories;


class StudentRepo extends Repository {

	static private $tableName = 'students';
	static private $columnNames = [
		'id',
		'family_name',
		'first_name',
		'enrolled',
		'classroom_id'
	];

	protected function getTableName() {
		return StudentRepo::$tableName;
	}

	protected function getColumnsNames() {
		return StudentRepo::$columnNames;
	}

	protected function translateTableRow( $row ) {
		$resource = [
			'id'          => $row['id'],
			'familyName'  => $row['family_name'],
			'firstName'   => $row['first_name'],
			'enrolled'    => $row['enrolled'] ? true : false,
			'classroomId' => $row['classroom_id']
		];

		return $resource;
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