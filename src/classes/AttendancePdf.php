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
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), '', 'LTR', 0, 'C', true );
        $this->ln();

        $i = 0;
        $d = new DateTime( $this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Student', 'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->format("M j"),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),  'LBR', 0, 'C', true );
        $this->Cell($this->colWidths[$i++], $this->getHeaderHeight(), 'Notes',   'LBR', 0, 'C', true );
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
        usort($students, function($a, $b) {
            if ($a['familyName'] > $b[ 'familyName' ]) {
                return 1;
            } else if ( $a[ 'familyName' ] < $b[ 'familyName' ]) {
                return -1;
            } else if ( $a[ 'firstName' ] > $b[ 'firstName' ]) {
                return 1;
            } else if ( $a[ 'firstName' ] < $b[ 'firstName' ]) {
                return -1;
            }
            return 0;
        });
        $classes = $this->getApi()->fetchClassrooms();
        $this->AliasNbPages();
        $this->colWidths = [50, 15, 15, 15, 15, 15, 50];
        $this->SetFillColor( 200 );
        foreach ( $classes as $class ) {
            $this->setTheClassroom($class);
            $this->AddPage('P');
            $this->SetFont('Arial', '', 10 );
            $totals = [];
            foreach (self::getDayAbbrevs() as $day) {
                $totals[$day] = 0;
            }
            foreach ( $students as $student ) {
                if ( $student[ 'classroomId' ] === $class[ 'id' ] ) {
                    $composite = $this->getCompositeSchedule( $student, $this->getWeekOf());
                    $student[ 'schedule' ] = [];
                    $notes = [
                        'HD'  => 0,
                        'HDL' => 0,
                        'FD' => 0
                    ];
                    $i = 0;
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, $student[ 'familyName' ] . ', ' . $student[ 'firstName' ], 1, 0 );
                    foreach ( self::getDayAbbrevs() as $day ) {
                        if ( null == $composite[$day]) {
                            $student['schedule'][$day] = false;
                        } else {
                            $student['schedule'][$day] = [];
                            if ($composite[$day]['Am']) array_push( $student['schedule'][$day], 'A');
                            if ($composite[$day]['Noon']) array_push( $student['schedule'][$day], 'L');
                            if ($composite[$day]['Pm']) array_push( $student['schedule'][$day], 'P');
                            $student['schedule'][$day] = implode( '/', $student['schedule'][$day]);
                            if ($student['schedule'][$day]) $totals[$day]++;

                            if (($composite[$day]['Am']) && ($composite[$day]['Pm'])) {
                                $notes['FD']++;
                            } else if (( $composite[$day]['Am']) || ($composite[$day]['Pm'])) {
                                if ($composite[$day]['Noon']) {
                                    $notes['HDL']++;
                                } else {
                                    $notes['HD']++;
                                }
                            }
                        }
                        $shade = !($composite[$day]['Am'] || $composite[$day]['Noon'] || $composite[$day]['Pm']);
                        $this->Cell($this->colWidths[$i++], $this->rowHeight, '', 1, 0, 'C', $shade );
                    }
                    foreach (['HD', 'HDL', 'FD'] as $k) {
                        if ($notes[$k] == 0 ) unset ($notes[$k]);
                        else $notes[$k] = $notes[$k] . $k;
                    }
                    $this->Cell($this->colWidths[$i++], $this->rowHeight, implode(',', $notes ), 1 );
                    $this->ln();
                }
            }
        }
        parent::Output();
    }

}