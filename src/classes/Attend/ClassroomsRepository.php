<?php

namespace Attend;


class ClassroomsRepository extends Repository
{
    public static function getTableName()
    {
        return 'classrooms';
    }

    public static function getColumnNames()
    {
        return [
            'id'       => [
                'select' => true,
                'insert' => false,
                'update' => false,
            ],
            'label'    => [
                'select' => true,
                'insert' => true,
                'update' => true,
            ],
            'ordering' => [
                'select' => true,
                'insert' => true,
                'update' => true,
            ]
        ];
    }
}
