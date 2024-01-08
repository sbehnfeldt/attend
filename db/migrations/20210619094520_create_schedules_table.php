<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateSchedulesTable extends AbstractMigration
{
    private $tableName = 'schedules';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('student_id', 'integer');
        $table->addColumn('schedule', 'integer');
        $table->addColumn('start_date', 'timestamp');
        $table->addColumn('entered_at', 'timestamp');
        $table->addForeignKey('student_id', 'students', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
