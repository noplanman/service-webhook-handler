<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Handlers;

use NPM\ServiceWebhookHandler\Utils;

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

    /**
     * @inheritdoc
     */
    protected function getVitalHeaders(): array
    {
        return [
            'signature' => 'HTTP_SIGNATURE',
            'repo_slug' => 'HTTP_TRAVIS_REPO_SLUG',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function validateSignature(string $signature, string $payload): bool
    {
        if ($pubkey = $this->getTravisPubKey()) {
            return 1 === openssl_verify($payload, base64_decode($signature), $pubkey);
        }

        return false;
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

        $api_config = Utils::fetchCacheableFile(
            $api_host . '/config',
            'travis-ci-api-config.json',
            self::API_CONFIG_CACHE_TIME
        );

        return json_decode($api_config, true) ?: [];
    }
}
