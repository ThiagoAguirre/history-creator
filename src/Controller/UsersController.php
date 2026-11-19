<?php
declare(strict_types=1);

namespace App\Controller;

use App\Model\Entity\User;
use App\Service\SocialLoginService;
use Cake\Core\Configure;
use Cake\Event\EventInterface;
use Cake\Http\Response;
use Cake\I18n\DateTime;
use Cake\Log\Log;
use Cake\Mailer\Mailer;
use Cake\Routing\Router;
use Cake\Utility\Security;
use Throwable;

/**
 * Users Controller: login, registration, password recovery and social login.
 *
 * @property \App\Model\Table\UsersTable $Users
 */
class UsersController extends AppController
{
    /**
     * Minutes a password reset link stays valid.
     */
    protected const RESET_TOKEN_TTL = 60;

    /**
     * @param \Cake\Event\EventInterface<\Cake\Controller\Controller> $event Event.
     * @return void
     */
    public function beforeFilter(EventInterface $event): void
    {
        parent::beforeFilter($event);

        $this->Authentication->allowUnauthenticated([
            'home',
            'login',
            'register',
            'forgotPassword',
            'resetPassword',
            'socialLogin',
            'socialCallback',
        ]);
    }

    /**
     * Home screen (public).
     *
     * @return void
     */
    public function home(): void
    {
    }

    /**
     * Login with e-mail and password.
     *
     * @return \Cake\Http\Response|null
     */
    public function login(): ?Response
    {
        $this->viewBuilder()->setLayout('auth');
        $this->request->allowMethod(['get', 'post']);
        $result = $this->Authentication->getResult();

        if ($result && $result->isValid()) {
            if ($this->request->is('post')) {
                $this->touchLastLogin((int)$this->Authentication->getIdentifier());
            }

            return $this->redirect($this->Authentication->getLoginRedirect() ?? '/');
        }

        if ($this->request->is('post')) {
            $this->Flash->error('E-mail ou senha inválidos.');
        }

        $this->set('socialProviders', SocialLoginService::enabledProviders());

        return null;
    }

    /**
     * Create a new account and log the user in.
     *
     * @return \Cake\Http\Response|null
     */
    public function register(): ?Response
    {
        $this->viewBuilder()->setLayout('auth');
        if ($this->Authentication->getIdentity()) {
            return $this->redirect('/');
        }

        $user = $this->Users->newEmptyEntity();

        if ($this->request->is('post')) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => ['name', 'email', 'password'],
                'validate' => 'password',
            ]);
            $user->last_login_at = DateTime::now();

            if ($this->Users->save($user)) {
                $this->Authentication->setIdentity($user);
                $this->Flash->success('Conta criada com sucesso. Bem-vindo(a)!');

                return $this->redirect('/');
            }
            $this->Flash->error('Não foi possível criar sua conta. Verifique os campos abaixo.');
        }

        $this->set('user', $user);
        $this->set('socialProviders', SocialLoginService::enabledProviders());

        return null;
    }

    /**
     * Send a password reset link by e-mail.
     *
     * The response is the same whether or not the e-mail exists, to avoid account enumeration.
     *
     * @return \Cake\Http\Response|null
     */
    public function forgotPassword(): ?Response
    {
        if ($this->request->is('post')) {
            $email = mb_strtolower(trim((string)$this->request->getData('email')));
            $user = $email === '' ? null : $this->Users->find('active')->where(['email' => $email])->first();

            if ($user) {
                $token = bin2hex(Security::randomBytes(32));
                $user->password_reset_token = hash('sha256', $token);
                $user->password_reset_expires = DateTime::now()->addMinutes(self::RESET_TOKEN_TTL);
                $this->Users->saveOrFail($user);

                $this->sendResetEmail($user, $token);
            }

            $this->Flash->success(
                'Se o e-mail estiver cadastrado, você receberá um link para redefinir sua senha.',
            );

            return $this->redirect(['action' => 'login']);
        }

        return null;
    }

    /**
     * Define a new password from a reset link.
     *
     * @param string $token Raw reset token received by e-mail.
     * @return \Cake\Http\Response|null
     */
    public function resetPassword(string $token): ?Response
    {
        $user = $this->Users->find('active')
            ->where([
                'password_reset_token' => hash('sha256', $token),
                'password_reset_expires >' => DateTime::now(),
            ])
            ->first();

        if (!$user) {
            $this->Flash->error('Link de redefinição inválido ou expirado. Solicite um novo.');

            return $this->redirect(['action' => 'forgotPassword']);
        }

        if ($this->request->is(['post', 'put'])) {
            $user = $this->Users->patchEntity($user, $this->request->getData(), [
                'fields' => ['password'],
                'validate' => 'password',
            ]);
            $user->password_reset_token = null;
            $user->password_reset_expires = null;

            if ($this->Users->save($user)) {
                $this->Flash->success('Senha redefinida. Faça login com sua nova senha.');

                return $this->redirect(['action' => 'login']);
            }
            $this->Flash->error('Não foi possível redefinir a senha. Verifique os campos abaixo.');
        }

        $this->set(compact('user', 'token'));

        return null;
    }

    /**
     * Redirect to the social provider authorization page.
     *
     * @param string $provider Provider key (google, github).
     * @return \Cake\Http\Response
     */
    public function socialLogin(string $provider): Response
    {
        $state = bin2hex(Security::randomBytes(16));
        $this->request->getSession()->write("SocialLogin.state.{$provider}", $state);

        $url = (new SocialLoginService())->getAuthorizationUrl($provider, $this->callbackUrl($provider), $state);

        return $this->redirect($url);
    }

    /**
     * Handle the provider callback: validate state, resolve the user and log in.
     *
     * @param string $provider Provider key (google, github).
     * @return \Cake\Http\Response
     */
    public function socialCallback(string $provider): Response
    {
        $session = $this->request->getSession();
        $expectedState = $session->consume("SocialLogin.state.{$provider}");
        $state = (string)$this->request->getQuery('state');
        $code = (string)$this->request->getQuery('code');

        if (!$expectedState || !hash_equals($expectedState, $state) || $code === '') {
            $this->Flash->error('Não foi possível concluir o login social. Tente novamente.');

            return $this->redirect(['action' => 'login']);
        }

        try {
            $profile = (new SocialLoginService())->fetchUser($provider, $code, $this->callbackUrl($provider));
            $user = $this->Users->findOrCreateFromSocial($provider, $profile);
        } catch (Throwable $e) {
            Log::error("Social login ({$provider}) failed: " . $e->getMessage());
            $user = null;
        }

        if (!$user) {
            $this->Flash->error(
                'Não foi possível entrar com essa conta. Verifique se o e-mail dela está confirmado no provedor.',
            );

            return $this->redirect(['action' => 'login']);
        }

        $this->Authentication->setIdentity($user);
        $this->touchLastLogin($user->id);

        return $this->redirect('/');
    }

    /**
     * End the session.
     *
     * @return \Cake\Http\Response|null
     */
    public function logout(): ?Response
    {
        $this->request->allowMethod(['post']);
        $this->Authentication->logout();
        $this->Flash->success('Você saiu da sua conta.');

        return $this->redirect('/');
    }

    /**
     * @param int $userId User id.
     * @return void
     */
    protected function touchLastLogin(int $userId): void
    {
        $this->Users->updateAll(['last_login_at' => DateTime::now()], ['id' => $userId]);
    }

    /**
     * @param string $provider Provider key.
     * @return string Absolute callback URL registered at the provider.
     */
    protected function callbackUrl(string $provider): string
    {
        return Router::url(['controller' => 'Users', 'action' => 'socialCallback', $provider], true);
    }

    /**
     * @param \App\Model\Entity\User $user User.
     * @param string $token Raw reset token.
     * @return void
     */
    protected function sendResetEmail(User $user, string $token): void
    {
        $link = Router::url(['controller' => 'Users', 'action' => 'resetPassword', $token], true);

        try {
            $mailer = (new Mailer('default'))
                ->setTo($user->email, $user->name)
                ->setSubject('Redefinição de senha - History Creator')
                ->setEmailFormat('both')
                ->setViewVars(['name' => $user->name, 'link' => $link, 'ttl' => self::RESET_TOKEN_TTL]);
            $mailer->viewBuilder()->setTemplate('reset_password');
            $mailer->deliver();
        } catch (Throwable $e) {
            Log::error('Password reset e-mail failed: ' . $e->getMessage());
        }

        if (Configure::read('debug')) {
            Log::debug("Password reset link for {$user->email}: {$link}");
        }
    }
}
