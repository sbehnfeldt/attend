<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateIndividualPermissionsTable extends AbstractMigration
{
    private $tableName = 'individual_permissions';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('account_id', 'integer', ['null' => false]);
        $table->addForeignKey('account_id', 'accounts', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->addColumn('permissions_id', 'integer', ['null' => false]);
        $table->addForeignKey('permissions_id', 'permissions', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
