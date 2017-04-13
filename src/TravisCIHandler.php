<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Webhooks;

class TravisCIHandler extends WebhookHandler
{
    /**
     * @var string
     */
    const API_HOST = 'https://api.travis-ci.org';

    /**
     * @var int
     */
    const API_CONFIG_CACHE_TIME = 60;

    /**
     * @var string
     */
    protected $repo_slug;

    /**
     * @return string
     */
    public function getRepoSlug()
    {
        return $this->repo_slug;
    }

    protected function getVitalHeaders(): array
    {
        return [
            'signature' => 'HTTP_SIGNATURE',
            'repo_slug' => 'HTTP_TRAVIS_REPO_SLUG',
        ];
    }

    /**
     * Validate payload with given signature.
     *
     * @param string $signature As passed in the HTTP_SIGNATURE header.
     * @param string $payload
     *
     * @return bool
     */
    protected function validateSignature(string $signature, string $payload): bool
    {
        return 1 === openssl_verify($payload, base64_decode($signature), $this->getTravisPubKey());
    }

    /**
     * Get the (cached) public key from the Travis CI API.
     *
     * @return string
     */
    protected function getTravisPubKey(): string
    {
        $api_config = $this->getTravisApiConfig();

        return $api_config['config']['notifications']['webhook']['public_key'] ?? '';
    }

    /**
     * Get the Travis API config.
     *
     * @param string $api_host
     *
     * @return array
     */
    protected function getTravisApiConfig(string $api_host = ''): array
    {
        if ($api_host === '') {
            $api_host = self::API_HOST;
        }

        $api_config_file        = $api_host . '/config';
        $api_config_file_cached = '';

        if ($cache_dir = getenv('CACHE_DIR')) {
            $api_config_file_cached = $cache_dir . '/travis-ci-api-config.json';

            if (file_exists($api_config_file_cached)) {
                if (filemtime($api_config_file_cached) + self::API_CONFIG_CACHE_TIME > time()) {
                    $api_config_file = $api_config_file_cached;
                } else {
                    unlink($api_config_file_cached);
                }
            }
        }

        if ($api_config_file === $api_config_file_cached) {
            $api_config = (string) file_get_contents($api_config_file);
        } else {
            $ch = curl_init($api_config_file);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_USERAGENT, 'PHP-Service-Webhook-Handler');
            $api_config = curl_exec($ch);
            curl_close($ch);
        }

        return json_decode($api_config, true) ?: [];
    }
}
