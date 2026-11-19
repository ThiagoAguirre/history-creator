<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class CreateUserSocialAccounts extends BaseMigration
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
        $table = $this->table('user_social_accounts');

        $table
            ->addColumn('user_id', 'integer', [
                'null' => false,
            ])
            ->addColumn('provider', 'string', [
                'limit' => 50,
            ])
            ->addColumn('provider_user_id', 'string', [
                'limit' => 191,
            ])
            ->addColumn('created', 'datetime')
            ->addColumn('modified', 'datetime')
            ->addIndex(
                ['provider', 'provider_user_id'],
                ['unique' => true],
            )
            ->addIndex(['user_id'])
            ->addForeignKey(
                'user_id',
                'users',
                'id',
                ['delete' => 'CASCADE'],
            )
            ->create();
    }
}
