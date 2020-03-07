<?php


namespace Attend\Database;


class Exporter
{
    public function __invoke()
    {
        $accounts = AccountQuery::create()->find()->toArray();
        $classrooms = ClassroomQuery::create()->find()->toArray();
        $students = StudentQuery::create()->find()->toArray();
        $schedules = ScheduleQuery::create()->find()->toArray();
        $attendance = AttendanceQuery::create()->find()->toArray();


        return [
            'accounts' => $accounts,
            'classrooms' => $classrooms,
            'students' => $students,
            'schedules' => $schedules,
            'attendance' => $attendance
        ];
    }
}