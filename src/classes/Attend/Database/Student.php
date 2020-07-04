<?php

namespace Attend\Database;

use Attend\Database\Base\Student as BaseStudent;


class Student extends BaseStudent
{
    /** @var array Bit mask to apply to student schedule to determine which days/day parts they are scheduled */
    static private $decoder = [
        // am,   lunch,    pm
        [0x0001, 0x0020, 0x0400],  // Mon
        [0x0002, 0x0040, 0x0800],  // Tue
        [0x0004, 0x0080, 0x1000],  // Wed
        [0x0008, 0x0100, 0x2000],  // Thu
        [0x0010, 0x0200, 0x4000]   // Fri
    ];

    /** @var array Keep track of # of full days, half days and half day w/lunch for the full week */
    private $notes;


    public function __construct()
    {
        parent::__construct();
        $this->notes = [
            'FD' => 0,
            'HD' => 0,
            'HDL' => 0
        ];
    }


    /**
     * Return a string representing the student's attendance for a specified day
     *
     * @param \DateTime $weekOf
     * @param int $dayOf
     * @return string
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function writeSchedule(\DateTime $weekOf, int $dayOf): string
    {
        $decoder = Student::$decoder[$dayOf];
        $code = $this->getSchedules()[$this->getSchedules()->count() - 1]->getSchedule();

        if (($code & $decoder[0]) && ($code & $decoder[2])) {
            $x = 'FD';

        } else if ($code & $decoder[0]) {
            if ($code & $decoder[1]) {
                $x = 'HDL';
            } else {
                $x = 'HD';
            }

        } else if ($code & $decoder[ 2 ]) {
            if ($code & $decoder[ 1 ]) {
                $x = 'HDL';
            } else {
                $x = 'HD';
            }

        } else {
            return '';
        }
        $this->notes[$x]++;

        return $x;
    }


    /**
     * Return a string representing the number of each day part (half, full, lunch) the student attends in a week
     *
     * @return string
     */
    public function writeSummary()
    {
        $summary = [];
        if ($this->notes['FD']) {
            $summary[] = $this->notes['FD'] . 'FD';
        }
        if ($this->notes['HD']) {
            $summary[] = $this->notes['HD'] . 'HD';
        }
        if ($this->notes['HDL']) {
            $summary[] = $this->notes[ 'HDL' ] . 'HDL';
        }

        return implode(',', $summary);
    }
}
