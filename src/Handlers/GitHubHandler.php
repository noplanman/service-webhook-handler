<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Handlers;

use NPM\ServiceWebhookHandler\Utils;

class GitHubHandler extends WebhookHandler
{
    /**
     * @var string
     */
    const API_HOST = 'https://api.github.com';

    /**
     * @var int
     */
    const API_META_CACHE_TIME = 60;

    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $event;

    /**
     * @var string
     */
    protected $delivery;

    /**
     * @var array
     */
    protected $options;

    /**
     * GitHub constructor.
     *
     * @param string $secret
     * @param array  $options
     */
    public function __construct(string $secret, array $options = [])
    {
        $this->secret = $secret;

        $this->options = array_merge([
            'validate_ip' => true,
        ], $options);
    }

    /**
     * Check if the incoming IP is coming from a GitHub hook.
     *
     * To do this, we use the GitHub API meta endpoint to get a list of valid IPs.
     *
     * @return bool
     */
    protected function validateIp(): bool
    {
        $meta = $this->getGitHubApiMeta();

        $ip     = @$_SERVER['REMOTE_ADDR'];
        $ranges = (array) ($meta['hooks'] ?? []);

        // Check if IP comes from a GitHub hook.
        foreach ($ranges as $range) {
            if (Utils::cidrMatch($ip, $range)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @inheritdoc
     */
    protected function extraValidations(): bool
    {
        if ($this->options['validate_ip']) {
            return $this->validateIp();
        }

        return true;
    }

    /**
     * @return string
     */
    public function getDelivery(): string
    {
        return $this->delivery;
    }

    /**
     * @return string
     */
    public function getEvent(): string
    {
        return $this->event;
    }

    /**
     * @return string
     */
    public function getSecret(): string
    {
        return $this->secret;
    }

    /**
     * @inheritdoc
     */
    protected function getVitalHeaders(): array
    {
        return [
            'signature' => 'HTTP_X_HUB_SIGNATURE',
            'event'     => 'HTTP_X_GITHUB_EVENT',
            'delivery'  => 'HTTP_X_GITHUB_DELIVERY',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function validateSignature(string $signature, string $payload): bool
    {
        list($algo, $real_signature) = explode('=', $signature);

        if ($algo !== 'sha1') {
            // see https://developer.github.com/webhooks/securing/
            return false;
        }

        $payload_hash = hash_hmac($algo, $payload, $this->secret);

        return $payload_hash === $real_signature;
    }

    /**
     * Get the GitHub API meta.
     *
     * @param string $api_host
     *
     * @return array
     */
    protected function getGitHubApiMeta(string $api_host = ''): array
    {
        if ($api_host === '') {
            $api_host = self::API_HOST;
        }

        $api_meta = Utils::fetchCacheableFile(
            $api_host . '/meta',
            'github-api-meta.json',
            self::API_META_CACHE_TIME
        );

        return json_decode($api_meta, true) ?: [];
    }
}
