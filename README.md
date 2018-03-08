# service-webhook-handler
PHP library to handle Webhooks from various services.

Requires PHP 7.1+

Very basic functionality provided so far for:
- [GitHub][github-handler]
```php
use NPM\ServiceWebhookHandler\Handlers\GitHubHandler;

$handler = new GitHubHandler('webhook_secret');
if ($handler->validate()) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

- [Travis CI][travis-ci-handler]
```php
use NPM\ServiceWebhookHandler\Handlers\TravisCIHandler;

$handler = new TravisCIHandler();
if ($handler->validate()) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

- [Telegram Login][telegram-login-handler]
```php
use NPM\ServiceWebhookHandler\Handlers\TelegramLoginHandler;

$handler = new TelegramLoginHandler('123:BOT_API_KEY');
if ($handler->validate(json_encode($_GET))) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

[github-handler]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/GitHubHandler.php
[travis-ci-handler]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/TravisCIHandler.php
[telegram-login-handler]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/TelegramLoginHandler.php
