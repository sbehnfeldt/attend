<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAttendanceTable extends AbstractMigration
{
    private $tableName = 'attendance';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('student_id', 'integer');
        $table->addForeignKey('student_id', 'students', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->addColumn('check_in', 'timestamp');
        $table->addColumn('check_out', 'timestamp');
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
