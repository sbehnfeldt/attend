<?php


class AttendancePdf extends AttendPdf
{

    private $classes;
    private $students;
    private $schedules;

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


    protected function foo($url)
    {
        $contents = file_get_contents($url);
        $json     = json_decode($contents, true);
        $records  = $json[ 'data' ];
        $return   = [];
        foreach ($records as $record) {
            $return[ $record[ 'id' ] ] = $record;
        }

        return $return;
    }

    public function Output()
    {
        $this->classes   = $this->foo('http://' . $_SERVER[ 'HTTP_HOST' ] . '/attend-api/classrooms');
        $this->students  = $this->foo('http://' . $_SERVER[ 'HTTP_HOST' ] . '/attend-api/students');
        $this->schedules = $this->foo('http://' . $_SERVER[ 'HTTP_HOST' ] . '/attend-api/schedules');

        foreach ($this->classes as $classroomId => &$class) {
            $class[ 'students' ] = [];
        }
        unset($class);


        foreach ($this->students as $studentId => &$student) {
            if ('1' !== $student[ 'enrolled' ]) {
                continue;
            }
            $student[ 'schedules' ]                        = [];
            $classroomId                                   = $student[ 'classroom_id' ];
            $this->classes[ $classroomId ][ 'students' ][] = $studentId;
        }
        unset($student);

        foreach ($this->schedules as $scheduleId => &$schedule) {
            $studentId                                     = $schedule[ 'student_id' ];
            $this->students[ $studentId ][ 'schedules' ][] = $schedule;
        }
        unset($schedule);


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
            $student               = $this->students[ $studentId ];
            $schedule              = $student[ 'schedules' ][ 0 ];
            $student[ 'schedule' ] = [];
            $notes                 = [
                'HD'  => 0,
                'HDL' => 0,
                'FD'  => 0
            ];
            $i                     = 0;
            $this->Cell($this->colWidths[ $i++ ], $this->rowHeight,
                $student[ 'family_name' ] . ', ' . $student[ 'first_name' ], 1, 0);

            /*
            foreach (self::getDayAbbrevs() as $day) {
                if (null == $schedule[ $day ]) {
                    $student[ 'schedule' ][ $day ] = false;
                } else {
                    $student[ 'schedule' ][ $day ] = [];
                    if ($schedule[ $day ][ 'Am' ]) {
                        array_push($student[ 'schedule' ][ $day ], 'A');
                    }
                    if ($schedule[ $day ][ 'Noon' ]) {
                        array_push($student[ 'schedule' ][ $day ], 'L');
                    }
                    if ($schedule[ $day ][ 'Pm' ]) {
                        array_push($student[ 'schedule' ][ $day ], 'P');
                    }
                    $student[ 'schedule' ][ $day ] = implode('/', $student[ 'schedule' ][ $day ]);
                    if ($student[ 'schedule' ][ $day ]) {
                        $totals[ $day ]++;
                    }

                    if (($schedule[ $day ][ 'Am' ]) && ($schedule[ $day ][ 'Pm' ])) {
                        $notes[ 'FD' ]++;
                    } else if (($schedule[ $day ][ 'Am' ]) || ($schedule[ $day ][ 'Pm' ])) {
                        if ($schedule[ $day ][ 'Noon' ]) {
                            $notes[ 'HDL' ]++;
                        } else {
                            $notes[ 'HD' ]++;
                        }
                    }
                }
                $shade = ! ($schedule[ $day ][ 'Am' ] || $schedule[ $day ][ 'Noon' ] || $schedule[ $day ][ 'Pm' ]);
                $this->Cell($this->colWidths[ $i++ ], $this->rowHeight, '', 1, 0, 'C', $shade);
            }
            */
            foreach (['HD', 'HDL', 'FD'] as $k) {
                if ($notes[ $k ] == 0) {
                    unset ($notes[ $k ]);
                } else {
                    $notes[ $k ] = $notes[ $k ] . $k;
                }
            }
            $this->Cell($this->colWidths[ $i++ ], $this->rowHeight, implode(',', $notes), 1);
            $this->ln();
        }
    }


}