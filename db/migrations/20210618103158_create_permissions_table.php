<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class  CreatePermissionsTable extends AbstractMigration
{
    private $tableName = 'permissions';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('slug', 'string', [
            'limit'   => 127,
            'null'    => false,
            'comment' => 'Human-readable mnemonic for the permission name'
        ]);
        $table->addColumn('description', 'string', [
            'limit'   => 1024,
            'null'    => false,
            'default' => '',
            'comment' => 'Description of what the permission permits'
        ]);
        $table->addIndex('slug', ['unique' => true]);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
