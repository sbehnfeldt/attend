<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGroupPermissionsTable extends AbstractMigration
{
    private $tableName = 'group_permissions';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('group_id', 'integer', ['null' => false]);
        $table->addColumn('permission_id', 'integer', ['null' => false]);

        $table->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->addForeignKey('permission_id', 'permissions', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);

        $table->addIndex(('group_id'));   // Index all permissions belonging to a group

        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
