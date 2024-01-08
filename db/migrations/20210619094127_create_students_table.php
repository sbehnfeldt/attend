<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateStudentsTable extends AbstractMigration
{
    private $tableName = 'students';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('family_name', 'string', ['limit' => 255]);
        $table->addColumn('first_name', 'string', ['limit' => 255]);
        $table->addColumn('enrolled', 'boolean');
        $table->addColumn('classroom_id', 'integer');
        $table->addForeignKey('classroom_id', 'classrooms', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }

}
