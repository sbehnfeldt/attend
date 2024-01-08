<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateTokenAuthsTable extends AbstractMigration
{
    private $tableName = 'token_auths';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('cookie_hash', 'string', ['limit' => 255]);
        $table->addColumn('expires', 'timestamp');
        $table->addColumn('account_id', 'integer', ['null' => false]);
        $table->addForeignKey('account_id', 'accounts', 'id', ['delete' => 'CASCADE', 'update' => 'NO_ACTION']);
        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
