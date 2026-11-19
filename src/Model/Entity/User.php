<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Authentication\PasswordHasher\DefaultPasswordHasher;
use Cake\ORM\Entity;

/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property string|null $password
 * @property string $status
 * @property \Cake\I18n\DateTime|null $email_verified_at
 * @property \Cake\I18n\DateTime|null $last_login_at
 * @property string|null $password_reset_token
 * @property \Cake\I18n\DateTime|null $password_reset_expires
 * @property \Cake\I18n\DateTime $created
 * @property \Cake\I18n\DateTime $modified
 * @property \App\Model\Entity\UserSocialAccount[] $user_social_accounts
 */
class User extends Entity
{
    public const STATUS_ACTIVE = 'active';

    /**
     * @var array<string, bool>
     */
    protected array $_accessible = [
        'name' => true,
        'email' => true,
        'password' => true,
    ];

    /**
     * @var list<string>
     */
    protected array $_hidden = [
        'password',
        'password_reset_token',
    ];

    /**
     * Hash the password whenever it is set.
     *
     * @param string|null $password Plain password.
     * @return string|null
     */
    protected function _setPassword(?string $password): ?string
    {
        if ($password === null || $password === '') {
            return null;
        }

        return (new DefaultPasswordHasher())->hash($password);
    }
}
