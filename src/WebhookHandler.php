<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Webhooks;

abstract class WebhookHandler
{
    /**
     * @var array
     */
    protected $data;

    /**
     * @var string
     */
    protected $signature;

    /**
     * Get payload data.
     *
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * Define a list of vital headers required for the validation.
     *
     * @return array
     */
    abstract protected function getVitalHeaders(): array;

    /**
     * Validate payload with given signature.
     *
     * @param string $signature As passed in the HTTP_X_HUB_SIGNATURE header.
     * @param string $payload
     *
     * @return bool
     */
    abstract protected function validateSignature(string $signature, string $payload): bool;

    /**
     * Validate the vital headers and set member variables.
     *
     * @return bool
     */
    protected function validateHeaders(): bool
    {
        $validated = true;
        foreach ($this->getVitalHeaders() as $key => $header) {
            if (($this->$key = @$_SERVER[$header]) === null) {
                $validated = false;
            }
        }

        return $validated;
    }

    /**
     * Validate the payload.
     *
     * @param string $payload
     *
     * @return bool
     */
    public function validate(string $payload = ''): bool
    {
        if (!$this->validateHeaders()) {
            return false;
        }

        $payload = $this->loadPayload($payload);

        if (!$this->validateSignature($this->signature, $payload)) {
            return false;
        }

        $this->data = json_decode($payload, true);

        return true;
    }

    /**
     * Load payload from php://input or use the passed one.
     *
     * @param string $payload
     *
     * @return string
     */
    protected function loadPayload($payload = ''): string
    {
        $payload !== '' || $payload = (string) file_get_contents('php://input');

        // Check if the payload is json or urlencoded.
        if (strpos($payload, 'payload=') === 0) {
            $payload = (string) substr(urldecode($payload), 8);
        }

        return $payload;
    }
}
