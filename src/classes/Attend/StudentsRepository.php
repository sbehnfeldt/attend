<?php

namespace Attend;


class StudentsRepository extends Repository
{
    static public function getTableName()
    {
        return 'students';
    }

    static public function getColumnNames()
    {
        return [
            'id'           => [
                'select' => true,
                'insert' => false,
                'update' => false,
            ],
            'family_name'  => [
                'select' => true,
                'insert' => true,
                'update' => true,
            ],
            'first_name'   => [
                'select' => true,
                'insert' => true,
                'update' => true,
            ],
            'enrolled'     => [
                'select' => true,
                'insert' => true,
                'update' => true,
            ],
            'classroom_id' => [
                'select' => true,
                'insert' => true,
                'update' => true,
            ]

        ];
    }
}
