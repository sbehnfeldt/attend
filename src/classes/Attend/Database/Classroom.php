<?php

namespace Attend\Database;

use Attend\Database\Base\Classroom as BaseClassroom;

/**
 * Skeleton subclass for representing a row from the 'classrooms' table.
 *
 *
 *
 * You should add additional methods to this class to meet the
 * application requirements.  This class will only be generated as
 * long as it does not already exist in the output directory.
 *
 */
class Classroom extends BaseClassroom
{

    /**
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function getDailyTotals()
    {
        $totals = [0, 0, 0, 0, 0];
        $students = $this->getStudents();
        for ($i = 0; $i < count($totals); $i++) {
            foreach ($students as $student) {
                if ($student->attendsOnDayOfWeek($i)) {
                    $totals[$i]++;
                }
            }
        }
        return $totals;
    }
}