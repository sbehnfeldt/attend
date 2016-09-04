<?php


class SigninPdf extends AttendPdf
{

    public function __construct($api = null)
    {
        parent::__construct($api);
        $this->setRowHeight(15);
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Sign-In', 0, 0, 'C');

        $this->SetFont('Arial', '', 12);
        $this->ln();
        $this->Cell(0, 10, $this->getTheClassroom()[ 'name' ], 0, 0, 'L');
        $this->Cell(0, 10, 'Week of ' . $this->getWeekOf()->format('M j, Y'), 0, 1, 'R');

        // Draw the table header
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor( 200 );

        $i = 0;
        $d = new DateTime( $this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), '', 'LTR', 0, 'C', true );

        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->format('D'),  'LTR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add( new DateInterval('P1D'))->format('D'), 'LTR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add( new DateInterval('P1D'))->format('D'), 'LTR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add( new DateInterval('P1D'))->format('D'), 'LTR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add( new DateInterval('P1D'))->format('D'), 'LTR', 0, 'C', true );
        $this->ln();

        $i = 0;
        $d = new DateTime( $this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), '', 'LR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->format('M j'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M d'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M d'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M d'),   'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M d'),   'LBR', 0, 'C', true );
        $this->ln();

        $i = 0;
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), '', 'LR', 0, 'C', true );
        foreach ( self::getDayAbbrevs() as $day ) {
            $this->Cell($this->colWidths[$i]/2, $this->getHeaderHeight(), "In",  'LBR', 0, 'C', true );
            $this->Cell($this->colWidths[$i++]/2, $this->getHeaderHeight(), "",  'LR', 0, 'C', true );
        }
        $this->ln();


        $i = 0;
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Student', 'LBR', 0, 'C', true );
        foreach ( self::getDayAbbrevs() as $day ) {
            $this->Cell($this->colWidths[$i]/2, $this->getHeaderHeight(), "Out",  'LBR', 0, 'C', true );
            $this->Cell($this->colWidths[$i++]/2, $this->getHeaderHeight(), "Initial",  'LBR', 0, 'C', true );
        }
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
        $this->colWidths = [55, 45, 45, 45, 45, 45];
        foreach ( $classes as $class ) {
            $this->setTheClassroom($class);
            $this->AddPage('L');
            $this->SetFont('Arial', '', 10 );
            foreach ( $students as $student ) {
                $i = 0;
                if ( $student[ 'classroomId' ] === $class[ 'id' ] ) {
                    $this->Cell( $this->colWidths[$i++], $this->getRowHeight(), $student[ 'firstName' ] . ' ' . $student[ 'familyName' ], 1, 0 );
                    foreach ( self::getDayAbbrevs() as $day ) {
                        $x = $this->GetX();
                        $y = $this->GetY();
                        $this->SetDrawColor(175, 175, 175);
                        $this->Line( $x, $y + ($this->getRowHeight() / 2), $x + $this->colWidths[$i], $y + ($this->getRowHeight()/2));
                        $this->SetDrawColor( 0, 0, 0 );


                        $this->Cell($this->colWidths[$i]/2, $this->getRowHeight(), "",  1, 0, 'C', false );
                        $this->Cell($this->colWidths[$i++]/2, $this->getRowHeight(), "",  1, 0, 'C', false );
                    }
                    $this->ln();
                }
            }
        }
        parent::Output();
    }
}