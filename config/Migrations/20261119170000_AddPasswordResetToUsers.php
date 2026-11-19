<?php
declare(strict_types=1);

use Migrations\BaseMigration;

class AddPasswordResetToUsers extends BaseMigration
{
    /**
     * Adds the columns used by the forgot/reset password flow.
     *
     * @return void
     */
    public function change(): void
    {
        $this->table('users')
            ->addColumn('password_reset_token', 'string', [
                'limit' => 64,
                'null' => true,
                'default' => null,
            ])
            ->addColumn('password_reset_expires', 'datetime', [
                'null' => true,
                'default' => null,
            ])
            ->addIndex(['password_reset_token'])
            ->update();
    }
}
