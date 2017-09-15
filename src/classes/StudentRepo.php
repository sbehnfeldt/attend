<?php

use PDO;

class StudentRepo {

	/** @var  PDO */
	private $pdo;

	public function __construct( $pdo ) {
		$this->pdo = $pdo;
	}

	public function select() {
		$students = [ ];
		$rows     = $this->pdo->query( 'select id, family_name, first_name, enrolled, classroom_id from students' );
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
		$sth      = $this->pdo->prepare( 'select id, family_name, first_name, enrolled, classroom_id from students where id = :id' );
		$rows     = $sth->execute( [ ':id' => $id ] );

		while ( $row = $sth->fetch( )) {
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

}