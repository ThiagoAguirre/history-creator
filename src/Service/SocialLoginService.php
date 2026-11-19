<?php
declare(strict_types=1);

namespace App\Service;

use Cake\Core\Configure;
use Cake\Http\Client;
use Cake\Http\Exception\NotFoundException;
use RuntimeException;

/**
 * Minimal OAuth 2.0 client for the social login providers (Google and GitHub).
 *
 * Providers are configured under the `SocialLogin` key (see config/app_local.php).
 */
class SocialLoginService
{
    /**
     * Static endpoints for each supported provider.
     *
     * @var array<string, array<string, string>>
     */
    protected const PROVIDERS = [
        'google' => [
            'label' => 'Google',
            'authorizeUrl' => 'https://accounts.google.com/o/oauth2/v2/auth',
            'tokenUrl' => 'https://oauth2.googleapis.com/token',
            'scope' => 'openid email profile',
        ],
        'github' => [
            'label' => 'GitHub',
            'authorizeUrl' => 'https://github.com/login/oauth/authorize',
            'tokenUrl' => 'https://github.com/login/oauth/access_token',
            'scope' => 'read:user user:email',
        ],
    ];

    protected Client $http;

    /**
     * @param \Cake\Http\Client|null $http HTTP client.
     */
    public function __construct(?Client $http = null)
    {
        $this->http = $http ?? new Client(['timeout' => 15]);
    }

    /**
     * Providers that have client credentials configured.
     *
     * @return array<string, string> provider key => label
     */
    public static function enabledProviders(): array
    {
        $enabled = [];
        foreach (self::PROVIDERS as $key => $provider) {
            if (Configure::read("SocialLogin.{$key}.clientId") && Configure::read("SocialLogin.{$key}.clientSecret")) {
                $enabled[$key] = $provider['label'];
            }
        }

        return $enabled;
    }

    /**
     * Build the provider authorization URL.
     *
     * @param string $provider Provider key.
     * @param string $redirectUri Callback URL.
     * @param string $state Anti-CSRF state value.
     * @return string
     */
    public function getAuthorizationUrl(string $provider, string $redirectUri, string $state): string
    {
        $config = $this->config($provider);

        $params = [
            'client_id' => $config['clientId'],
            'redirect_uri' => $redirectUri,
            'response_type' => 'code',
            'scope' => $config['scope'],
            'state' => $state,
        ];
        if ($provider === 'google') {
            $params['prompt'] = 'select_account';
        }

        return $config['authorizeUrl'] . '?' . http_build_query($params);
    }

    /**
     * Exchange the authorization code and fetch the normalized user profile.
     *
     * @param string $provider Provider key.
     * @param string $code Authorization code.
     * @param string $redirectUri Callback URL (must match the authorize request).
     * @return array{id: string, email: string|null, email_verified: bool, name: string|null}
     */
    public function fetchUser(string $provider, string $code, string $redirectUri): array
    {
        $config = $this->config($provider);

        $response = $this->http->post($config['tokenUrl'], [
            'client_id' => $config['clientId'],
            'client_secret' => $config['clientSecret'],
            'code' => $code,
            'redirect_uri' => $redirectUri,
            'grant_type' => 'authorization_code',
        ], ['headers' => ['Accept' => 'application/json']]);

        $token = $response->getJson()['access_token'] ?? null;
        if (!$response->isOk() || !$token) {
            throw new RuntimeException("Falha ao obter o token de acesso de {$provider}.");
        }

        return match ($provider) {
            'google' => $this->fetchGoogleUser($token),
            'github' => $this->fetchGithubUser($token),
        };
    }

    /**
     * @param string $token Access token.
     * @return array{id: string, email: string|null, email_verified: bool, name: string|null}
     */
    protected function fetchGoogleUser(string $token): array
    {
        $data = $this->getJson('https://openidconnect.googleapis.com/v1/userinfo', $token);

        return [
            'id' => (string)$data['sub'],
            'email' => $data['email'] ?? null,
            'email_verified' => (bool)($data['email_verified'] ?? false),
            'name' => $data['name'] ?? null,
        ];
    }

    /**
     * @param string $token Access token.
     * @return array{id: string, email: string|null, email_verified: bool, name: string|null}
     */
    protected function fetchGithubUser(string $token): array
    {
        $data = $this->getJson('https://api.github.com/user', $token);

        $email = null;
        $verified = false;
        foreach ($this->getJson('https://api.github.com/user/emails', $token) as $item) {
            if (!empty($item['primary'])) {
                $email = $item['email'] ?? null;
                $verified = (bool)($item['verified'] ?? false);
                break;
            }
        }

        return [
            'id' => (string)$data['id'],
            'email' => $email,
            'email_verified' => $verified,
            'name' => $data['name'] ?? $data['login'] ?? null,
        ];
    }

    /**
     * @param string $url API URL.
     * @param string $token Access token.
     * @return array<mixed>
     */
    protected function getJson(string $url, string $token): array
    {
        $response = $this->http->get($url, [], [
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
                'User-Agent' => 'history-creator',
            ],
        ]);
        $data = $response->getJson();
        if (!$response->isOk() || !is_array($data)) {
            throw new RuntimeException('Falha ao consultar o perfil do usuário no provedor.');
        }

        return $data;
    }

    /**
     * @param string $provider Provider key.
     * @return array<string, string>
     */
    protected function config(string $provider): array
    {
        if (!isset(self::enabledProviders()[$provider])) {
            throw new NotFoundException('Provedor de login social não disponível.');
        }

        return self::PROVIDERS[$provider] + (array)Configure::read("SocialLogin.{$provider}");
    }
}
