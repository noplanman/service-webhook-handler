<?php declare(strict_types=1);

namespace NPM\ServiceWebhookHandler\Handlers;

class TelegramLoginHandler extends WebhookHandler
{
    /**
     * @var string
     */
    protected $bot_api_key;

    /**
     * Telegram Login constructor.
     *
     * @param string $bot_api_key
     */
    public function __construct(string $bot_api_key)
    {
        $this->bot_api_key = $bot_api_key;

        // Required but not necessary for Telegram Login.
        $this->signature = '';
    }

    /**
     * @return string
     */
    public function getBotApiKey(): string
    {
        return $this->bot_api_key;
    }

    /**
     * @inheritdoc
     */
    protected function getVitalHeaders(): array
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    protected function validateSignature(string $signature, string $payload): bool
    {
        $auth_data = json_decode($payload, true) ?: [];

        // Hash is bare minimum to check if incoming payload is valid.
        if (!array_key_exists('hash', $auth_data)) {
            return false;
        }

        $check_hash = $auth_data['hash'];
        unset($auth_data['hash']);

        $data_check_arr = [];
        foreach ($auth_data as $key => $value) {
            $data_check_arr[] = "{$key}={$value}";
        }
        sort($data_check_arr);

        $data_check_string = implode("\n", $data_check_arr);
        $secret_key        = hash('sha256', $this->bot_api_key, true);
        $hash              = hash_hmac('sha256', $data_check_string, $secret_key);

        if (strcmp($hash, $check_hash) !== 0) {
            // Data is NOT from Telegram.
            return false;
        }
        if ((time() - $auth_data['auth_date']) > 86400) {
            // Data is outdated.
            return false;
        }

        return true;
    }
}
