<?php

declare(strict_types=1);

use Phinx\Migration\AbstractMigration;

final class CreateLoginAttemptsTable extends AbstractMigration
{
    private $tableName = 'login_attempts';

    public function up(): void
    {
        $table = $this->table($this->tableName);
        $table->addColumn('attempted_at', 'timestamp');
        $table->addColumn('username', 'string', ['limit' => 63]);
        $table->addColumn('pass', 'boolean');
        $table->addColumn('note', 'string', ['limit' => 255]);

        $table->save();
    }

    public function down(): void
    {
        $this->table($this->tableName)->drop()->save();
    }
}
