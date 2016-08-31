<?php


class AttendancePdf  extends AttendPdf
{

    public function __construct($api = null)
    {
        parent::__construct($api);
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Attendance', 0, 0, 'C');

        $this->SetFont('Arial', '', 12);
        $this->ln();
        $this->Cell(0, 10, $this->getTheClassroom()[ 'name' ], 0, 0, 'L');
        $this->Cell(0, 10, 'Week of ' . $this->getWeekOf(), 0, 1, 'R');

        // Draw the table header
        $i = 0;
        $this->SetFont('Arial', '', 10);
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Student', 1 );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Mon', 1 );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Tue', 1 );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Wed', 1 );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Thu', 1 );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Fri', 1 );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Notes', 1 );
        $this->ln();

    }

    public function Footer()
    {
        $this->SetY( -15 );
        $this->SetFont( 'Arial', 'I', 10);
        $this->Cell( 0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C' );
    }



    public function Output()
    {
        $students = $this->getApi()->fetchStudents();
        $classes = $this->getApi()->fetchClassrooms();
        $this->AliasNbPages();
        $this->colWidths = [60, 10, 10, 10, 10, 10, 60];
        foreach ( $classes as $class ) {
            $this->setTheClassroom($class);
            $this->AddPage('P');
            $this->SetFont('Arial', '', 10 );
            foreach ( $students as $student ) {
                if ( $student[ 'classroomId' ] === $class[ 'id' ] ) {
                    $i = 0;
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, $student[ 'familyName' ] . ', ' . $student[ 'firstName' ], 1, 0 );
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1 );
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1 );
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1 );
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1 );
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1 );
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1 );

                    $this->ln();
                }
            }
        }
        parent::Output();
    }

}