<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Webhooks;

class TravisCI implements IWebhook
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
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $repo_slug;

    /**
     * @inheritdoc
     */
    public function getData(): array
    {
        return $this->data;
    }

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
    public function validate(string $payload = ''): bool
    {
        $signature = @$_SERVER['HTTP_SIGNATURE'];
        $repo_slug = @$_SERVER['HTTP_TRAVIS_REPO_SLUG'];

        if (!isset($signature, $repo_slug)) {
            return false;
        }

        $payload !== '' || $payload = (string) file_get_contents('php://input');

        // Check if the payload is json or urlencoded.
        if (strpos($payload, 'payload=') === 0) {
            $payload = (string) substr(urldecode($payload), 8);
        }

        if (!$this->validateSignature($signature, $payload)) {
            return false;
        }

        $this->data      = json_decode($payload, true);
        $this->repo_slug = $repo_slug;

        return true;
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
     * @return array
     */
    protected function getTravisApiConfig(): array
    {
        $api_config_file        = self::API_HOST . '/config';
        $api_config_file_cached = '';

        if ($cache_dir = getenv('CACHE_DIR')) {
            $api_config_file_cached = $cache_dir . '/travis-ci-api.pubkey';

            if (file_exists($api_config_file_cached)) {
                if (filemtime($api_config_file_cached) + self::API_CONFIG_CACHE_TIME > time()) {
                    $api_config_file = $api_config_file_cached;
                } else {
                    unlink($api_config_file_cached);
                }
            }
        }

        $api_config = file_get_contents($api_config_file);
        if ($api_config_file_cached && $api_config) {
            file_put_contents($api_config_file_cached, $api_config);
        }

        return json_decode($api_config, true);
    }
}
