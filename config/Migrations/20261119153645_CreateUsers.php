<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUsers extends BaseMigration
{
    /**
     * Change Method.
     *
     * More information on this method is available here:
     * https://book.cakephp.org/migrations/5/guides/writing-migrations/migration-methods.html#the-change-method
     *
     * @return void
     */
    public function change(): void
    {
        $table = $this->table('users');

        $table
            ->addColumn('name', 'string', ['limit' => 150])
            ->addColumn('email', 'string', ['limit' => 191])
            ->addColumn('password', 'string', [
                'limit' => 255,
                'null' => true,
            ])
            ->addColumn('status', 'string', [
                'limit' => 20,
                'default' => 'active',
            ])
            ->addColumn('email_verified_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('last_login_at', 'datetime', [
                'null' => true,
            ])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(['email'], ['unique' => true])
            ->create();
    }
}
