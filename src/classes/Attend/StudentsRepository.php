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

    static protected function preProcessInserts($inserts)
    {
        if (array_key_exists('classroom_id', $inserts)) {
            $temp = json_decode($inserts[ 'classroom_id' ], true);
            if ($temp) {
                $inserts[ 'classroom_id' ] = $temp[ 'data' ];
            }
        }

        return parent::preProcessInserts($inserts); // TODO: Change the autogenerated stub
    }


    static protected function preProcessUpdates($updates)
    {
        // classroom_id may be null
        if (array_key_exists('classroom_id', $updates)) {
            $temp = json_decode($updates[ 'classroom_id' ]);
            if ($temp) {
                $updates[ 'classroom_id' ] = $temp;
            }
        }
        return parent::preProcessUpdates($updates); // TODO: Change the autogenerated stub
    }


}
