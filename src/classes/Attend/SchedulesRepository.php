<?php

namespace Attend;


class SchedulesRepository extends Repository
{
    static public function getTableName()
    {
        return 'schedules';
    }

    static public function getColumnNames()
    {
        return [
            'id'         => [
                'insert' => false,
                'update' => false,
                'select' => true
            ],
            'student_id' => [
                'insert' => true,
                'update' => true,
                'select' => true
            ],
            'schedule'   => [
                'insert' => true,
                'update' => true,
                'select' => true
            ]
        ];
    }


}