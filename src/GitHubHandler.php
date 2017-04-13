<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Webhooks;

class GitHubHandler extends WebhookHandler
{
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
     * GitHub constructor.
     *
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
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
        [$algo, $real_signature] = explode('=', $signature);

        if ($algo !== 'sha1') {
            // see https://developer.github.com/webhooks/securing/
            return false;
        }

        $payload_hash = hash_hmac($algo, $payload, $this->secret);

        return $payload_hash === $real_signature;
    }
}
