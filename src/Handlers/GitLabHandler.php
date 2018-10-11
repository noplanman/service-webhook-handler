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
     * @param string|null $secret
     */
    public function __construct(?string $secret = null)
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
        $vital_headers = [
            'event' => 'HTTP_X_GITLAB_EVENT',
        ];

        // The secret could be left out, so only enforce the check if it's here.
        if (isset($_SERVER['HTTP_X_GITLAB_TOKEN'])) {
            $vital_headers['token'] = 'HTTP_X_GITLAB_TOKEN';
        }

        return $vital_headers;
    }

    /**
     * @inheritdoc
     */
    protected function validateSignature(string $signature, string $payload): bool
    {
        return $this->token === null || $this->token === $this->secret;
    }
}
