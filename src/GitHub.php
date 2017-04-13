<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Webhooks;

class GitHub implements IWebhook
{
    /**
     * @var string
     */
    private $secret;

    /**
     * @var array
     */
    private $data;

    /**
     * @var string
     */
    private $event;

    /**
     * @var string
     */
    private $delivery;

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
     * @inheritdoc
     */
    public function getData(): array
    {
        return $this->data;
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
    public function validate(string $payload = ''): bool
    {
        $signature = @$_SERVER['HTTP_X_HUB_SIGNATURE'];
        $event     = @$_SERVER['HTTP_X_GITHUB_EVENT'];
        $delivery  = @$_SERVER['HTTP_X_GITHUB_DELIVERY'];

        if (!isset($signature, $event, $delivery)) {
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

        $this->data     = json_decode($payload, true);
        $this->event    = $event;
        $this->delivery = $delivery;

        return true;
    }

    /**
     * Validate payload with given signature.
     *
     * @param string $signature As passed in the HTTP_X_HUB_SIGNATURE header.
     * @param string $payload
     *
     * @return bool
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
