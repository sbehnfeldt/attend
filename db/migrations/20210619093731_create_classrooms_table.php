<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateClassroomsTable extends AbstractMigration
{
    private $tableName = 'classrooms';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('label', 'string', ['limit' => 63, 'comment' => 'Human-readable name of the classroom']);
        $table->addColumn('ordering', 'integer', ['comment' => 'Order among all classrooms']);
        $table->addColumn('created_at', 'timestamp');
        $table->addColumn('updated_at', 'timestamp');
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
