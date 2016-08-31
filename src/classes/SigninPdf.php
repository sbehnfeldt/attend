<?php


class SigninPdf extends AttendPdf
{

    public function Output()
    {
        $students = $this->api->fetchStudents();
        $classes = $this->api->fetchClassrooms();
        foreach ( $classes as $class ) {
            $this->setTheClassroom($class);
            $this->AddPage('L');
            $this->SetFont('Arial', '', 10 );
            foreach ( $students as $student ) {
                if ( $student[ 'classroomId' ] === $class[ 'id' ] ) {
                    $this->Cell( 40, 10, $student[ 'familyName' ] . ', ' . $student[ 'firstName' ], 0, 1 );
                }
            }
        }

        parent::Output();
    }
}