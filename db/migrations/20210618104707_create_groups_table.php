<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGroupsTable extends AbstractMigration
{
    private $tableName = 'groups';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('name', 'string', [
            'limit'   => 127,
            'null'    => false,
            'comment' => 'Human-readable name of the group'
        ]);
        $table->addColumn('description', 'string', [
            'limit'   => 1024,
            'null'    => false,
            'default' => '',
            'comment' => 'Description of the role or purpose of the group'
        ]);
        $table->addIndex('name', ['unique' => true]);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
