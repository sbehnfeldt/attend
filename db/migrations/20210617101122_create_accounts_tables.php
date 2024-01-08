<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateAccountsTables extends AbstractMigration
{
    private $tableName = 'accounts';

    public function up(): void
    {
        ////////////////////////////////////////////////////////////////////////////////
        /// User Accounts
        ////////////////////////////////////////////////////////////////////////////////
        $table = $this->table($this->tableName);
        $table->addColumn('username', 'string', ['limit' => 31, 'null' => false]);
        $table->addColumn('pwhash', 'string', ['limit' => 63, 'null' => false]);
        $table->addColumn('email', 'string', ['limit' => 255]);
        $table->addColumn('role', 'string', ['limit' => 31]);
        $table->addIndex('username', ['unique' => true]);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
