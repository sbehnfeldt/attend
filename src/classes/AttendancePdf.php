<?php


class AttendancePdf extends AttendPdf
{


    public function __construct()
    {
        parent::__construct();
    }

    public function Header()
    {
        $this->SetFont('Arial', 'B', 16);
        $this->Cell(0, 10, 'Attendance', 0, 0, 'C');

        $this->SetFont('Arial', '', 12);
        $this->ln();
        $this->Cell(0, 10, $this->getTheClassroom()[ 'label' ], 0, 0, 'L');
        $this->Cell(0, 10, 'Week of ' . $this->getWeekOf()->format('M j, Y'), 0, 1, 'R');

        // Draw the table header
        $this->SetFont('Arial', '', 10);
        $this->SetFillColor(200);

        $i = 0;
        $d = new DateTime($this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), '', 'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->format('D'), 'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('D'),
            'LTR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), '', 'LTR', 0, 'C', true);
        $this->ln();

        $i = 0;
        $d = new DateTime($this->getWeekOf()->format('Y-m-d'));
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), 'Student', 'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->format("M j"), 'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), $d->add(new DateInterval('P1D'))->format('M j'),
            'LBR', 0, 'C', true);
        $this->Cell($this->colWidths[ $i++ ], $this->getHeaderHeight(), 'Notes', 'LBR', 0, 'C', true);
        $this->ln();
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
    }


    public function Output()
    {
        $this->prepare();


        $this->AliasNbPages();
        $this->colWidths = [50, 15, 15, 15, 15, 15, 50];
        $this->SetFillColor(200);
        foreach ($this->classes as &$class) {
            usort($class[ 'students' ], function ($id1, $id2) {
                if ($this->students[ $id1 ][ 'family_name' ] > $this->students[ $id2 ][ 'family_name' ]) {
                    return 1;
                }
                if ($this->students[ $id1 ][ 'family_name' ] < $this->students[ $id2 ][ 'family_name' ]) {
                    return -1;
                }
                if ($this->students[ $id1 ][ 'first_name' ] > $this->students[ $id2 ][ 'first_name' ]) {
                    return 1;
                }
                if ($this->students[ $id1 ][ 'first_name' ] < $this->students[ $id2 ][ 'first_name' ]) {
                    return -1;
                }

                return 0;
            });
            $this->outputClassroom($class);
        }

        parent::Output();
    }

    private function outputClassroom($classroom)
    {
        $this->setTheClassroom($classroom);
        $this->AddPage('P');
        $totals = [];
        foreach ($classroom[ 'students' ] as $studentId) {
            $this->outputStudent($studentId);
        }
    }

    private function outputStudent($studentId)
    {
        static $decoder = [
            [0x0001, 0x0020, 0x0400],
            [0x0002, 0x0040, 0x0800],
            [0x0004, 0x0080, 0x1000],
            [0x0008, 0x0100, 0x2000],
            [0x0010, 0x0200, 0x4000]
        ];

        $student = $this->students[ $studentId ];
        if ('1' !== $student[ 'enrolled' ]) {
            return;
        }
        usort($student[ 'schedules' ], function ($id1, $id2) {
            $date1 = DateTime::createFromFormat('Y-m-d', $this->schedules[ $id1 ][ 'start_date' ]);
            $date2 = DateTime::createFromFormat('Y-m-d', $this->schedules[ $id2 ][ 'start_date' ]);
            if ($date1 < $date2) {
                return -1;
            }
            if ($date1 > $date2) {
                return 1;
            }

            return 0;
        });

        $this->Cell($this->colWidths[ 0 ], $this->rowHeight,
            $student[ 'family_name' ] . ', ' . $student[ 'first_name' ], 1, 0);

        $j        = 0;
        $thisWeek = $this->getWeekOf();
        $today    = DateTime::createFromFormat('Y-m-d',
            $this->schedules[ $student[ 'schedules' ][ $j ] ][ 'start_date' ]);
        $notes    = [
            'HD'  => 0,
            'HDL' => 0,
            'FD'  => 0
        ];
        for ($i = 0; $i < 5; $i++) {
            while (($j + 1) < count($student[ 'schedules' ])) {
                $next = DateTime::createFromFormat('Y-m-d',
                    $this->schedules[ $student[ 'schedules' ][ $j + 1 ] ][ 'start_date' ]);
                if ($next > $thisWeek) {
                    break;
                }
                $today = $next;
                $j++;
            }
            $s    = [
                'am'    => false,
                'pm'    => false,
                'lunch' => false
            ];
            $temp = $this->schedules[ $student[ 'schedules' ][ $j ] ][ 'schedule' ];
            if (0 != ($temp & $decoder[ $i ][ 0 ])) {
                $s[ 'am' ] = true;
            }
            if (0 != ($temp & $decoder[ $i ][ 1 ])) {
                $s[ 'lunch' ] = true;
            }
            if (0 != ($temp & $decoder[ $i ][ 2 ])) {
                $s[ 'pm' ] = true;
            }
            if ($s[ 'am' ] && $s[ 'pm' ]) {
                $notes[ 'FD' ]++;
            } else if ($s[ 'am' ]) {
                if ($s[ 'lunch' ]) {
                    $notes[ 'HDL' ]++;
                } else {
                    $notes[ 'HD' ]++;
                }
            } else if ($s[ 'pm' ]) {
                if ($s[ 'lunch' ]) {
                    $notes[ 'HDL' ]++;
                } else {
                    $notes[ 'HD' ]++;
                }
            }
            $shade = ! ($s[ 'am' ] || $s[ 'lunch' ] || $s[ 'pm' ]);
            $this->Cell($this->colWidths[ $i + 1 ], $this->rowHeight, '', 1, 0, 'C', $shade);
            $thisWeek->modify('+1 day');
            $today->modify('+1 day');
        }

        foreach (['HD', 'HDL', 'FD'] as $k) {
            if ($notes[ $k ] == 0) {
                unset ($notes[ $k ]);
            } else {
                $notes[ $k ] = $notes[ $k ] . $k;
            }
        }
        $this->Cell($this->colWidths[ 6 ], $this->rowHeight, implode(',', $notes), 1);
        $this->ln();
    }
}
