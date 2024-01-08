<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateGroupMembershipTable extends AbstractMigration
{
    private $tableName = 'group_members';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('group_id', 'integer', ['null' => false]);
        $table->addColumn('account_id', 'integer', ['null' => false]);

        $table->addForeignKey('group_id', 'groups', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->addForeignKey('account_id', 'accounts', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);

        $table->addIndex('group_id');     // Index members of a group
        $table->addIndex('account_id');   // Index groups a user is a member of
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
