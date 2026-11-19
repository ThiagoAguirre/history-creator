<?php
declare(strict_types=1);

namespace App\Model\Table;

use App\Model\Entity\User;
use ArrayObject;
use Cake\Event\EventInterface;
use Cake\I18n\DateTime;
use Cake\ORM\Query\SelectQuery;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * @property \App\Model\Table\UserSocialAccountsTable&\Cake\ORM\Association\HasMany $UserSocialAccounts
 * @method \App\Model\Entity\User newEmptyEntity()
 * @method \App\Model\Entity\User get(mixed $primaryKey, array|string $finder = 'all', \Psr\SimpleCache\CacheInterface|string|null $cache = null, \Closure|string|null $cacheKey = null, mixed ...$args)
 */
class UsersTable extends Table
{
    /**
     * @param array<string, mixed> $config Table config.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('users');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('UserSocialAccounts', [
            'foreignKey' => 'user_id',
            'dependent' => true,
        ]);
    }

    /**
     * Normalize the e-mail before validation.
     *
     * @param \Cake\Event\EventInterface $event Event.
     * @param \ArrayObject<string, mixed> $data Request data.
     * @param \ArrayObject<string, mixed> $options Options.
     * @return void
     */
    public function beforeMarshal(EventInterface $event, ArrayObject $data, ArrayObject $options): void
    {
        if (isset($data['email']) && is_string($data['email'])) {
            $data['email'] = mb_strtolower(trim($data['email']));
        }
        if (isset($data['name']) && is_string($data['name'])) {
            $data['name'] = trim($data['name']);
        }
    }

    /**
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->scalar('name')
            ->maxLength('name', 150)
            ->requirePresence('name', 'create')
            ->notEmptyString('name', 'Informe seu nome.');

        $validator
            ->email('email', false, 'Informe um e-mail válido.')
            ->maxLength('email', 191)
            ->requirePresence('email', 'create')
            ->notEmptyString('email', 'Informe seu e-mail.');

        $validator
            ->allowEmptyString('password');

        return $validator;
    }

    /**
     * Validation used by the register and reset password forms.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationPassword(Validator $validator): Validator
    {
        $validator = $this->validationDefault($validator);

        $validator
            ->requirePresence('password')
            ->notEmptyString('password', 'Informe uma senha.')
            ->minLength('password', 8, 'A senha deve ter pelo menos 8 caracteres.')
            ->requirePresence('password_confirm')
            ->notEmptyString('password_confirm', 'Confirme sua senha.')
            ->sameAs('password_confirm', 'password', 'As senhas não conferem.');

        return $validator;
    }

    /**
     * @param \Cake\ORM\RulesChecker $rules Rules checker.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->isUnique(['email'], 'Este e-mail já está cadastrado.'));

        return $rules;
    }

    /**
     * Only active users may authenticate.
     *
     * @param \Cake\ORM\Query\SelectQuery $query Query.
     * @return \Cake\ORM\Query\SelectQuery
     */
    public function findActive(SelectQuery $query): SelectQuery
    {
        return $query->where([$this->aliasField('status') => User::STATUS_ACTIVE]);
    }

    /**
     * Find the user linked to a social account, linking or creating one when needed.
     *
     * An existing local account is only linked when the provider confirms the e-mail is verified,
     * preventing account takeover through unverified provider e-mails.
     *
     * @param string $provider Provider key.
     * @param array{id: string, email: string|null, email_verified: bool, name: string|null} $profile Provider profile.
     * @return \App\Model\Entity\User|null Null when the account cannot be resolved.
     */
    public function findOrCreateFromSocial(string $provider, array $profile): ?User
    {
        $account = $this->UserSocialAccounts->find()
            ->contain(['Users'])
            ->where(['provider' => $provider, 'provider_user_id' => $profile['id']])
            ->first();
        if ($account) {
            return $account->user->status === User::STATUS_ACTIVE ? $account->user : null;
        }

        if (!$profile['email'] || !$profile['email_verified']) {
            return null;
        }

        return $this->getConnection()->transactional(function () use ($provider, $profile): ?User {
            $user = $this->find()->where(['email' => mb_strtolower($profile['email'])])->first();
            if ($user === null) {
                $user = $this->newEntity([
                    'name' => $profile['name'] ?: strstr($profile['email'], '@', true),
                    'email' => mb_strtolower($profile['email']),
                ]);
                $user->email_verified_at = DateTime::now();
                $this->saveOrFail($user);
            } elseif ($user->status !== User::STATUS_ACTIVE) {
                return null;
            }

            $this->UserSocialAccounts->saveOrFail($this->UserSocialAccounts->newEntity([
                'user_id' => $user->id,
                'provider' => $provider,
                'provider_user_id' => $profile['id'],
            ]));

            return $user;
        });
    }
}
