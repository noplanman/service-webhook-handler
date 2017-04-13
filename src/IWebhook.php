<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Webhooks;

interface IWebhook
{
    /**
     * Validate the payload.
     *
     * @param string $payload
     *
     * @return bool
     */
    public function validate(string $payload = ''): bool;

    /**
     * Get payload data.
     *
     * @return array
     */
    public function getData(): array;
}
