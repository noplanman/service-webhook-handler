<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Handlers;

class GitLabHandler extends WebhookHandler
{
    /**
     * @var string
     */
    protected $secret;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var string
     */
    protected $event;

    /**
     * GitLab constructor.
     *
     * @param string $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;

        // Required but not necessary for GitLab.
        $this->signature = '';
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
     * @return string
     */
    public function getToken(): string
    {
        return $this->token;
    }

    /**
     * @inheritdoc
     */
    protected function getVitalHeaders(): array
    {
        return [
            'token' => 'HTTP_X_GITLAB_TOKEN',
            'event' => 'HTTP_X_GITLAB_EVENT',
        ];
    }

    /**
     * @inheritdoc
     */
    protected function validateSignature(string $signature, string $payload): bool
    {
        return $this->token === $this->secret;
    }
}
