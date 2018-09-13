<?php

namespace Attend;


interface iRepository
{
    static public function getTableName();

    static public function getColumnNames();
}
