# [Service Webhook Handler][github-swh]

[![Minimum PHP Version][min-php-version-badge]][packagist-swh]
[![Latest Stable Version][latest-version-badge]][packagist-swh]
[![Total Downloads][total-downloads-badge]][packagist-swh]
[![License][license-badge]][license]

PHP library to handle Webhooks from various services.

## Installation

Installation is pretty straightforward:

### Require this package with [Composer][composer]

Either run this command in your command line:

```bash
composer require noplanman/service-webhook-handler
```

**or**

For existing Composer projects, edit your project's `composer.json` file to require `noplanman/service-webhook-handler`:

```yaml
"require": {
    "noplanman/service-webhook-handler": "^0.2"
}
```
and then run `composer update`

## Usage

Very basic functionality provided so far for:

### GitHub
[Docs][github-webhook-docs] - [`GitHubHandler.php`][github-handler-php]
```php
use NPM\ServiceWebhookHandler\Handlers\GitHubHandler;

$handler = new GitHubHandler('webhook_secret');
if ($handler->validate()) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

### GitLab
[Docs][gitlab-webhook-docs] - [`GitLabHandler.php`][gitlab-handler-php]
```php
use NPM\ServiceWebhookHandler\Handlers\GitLabHandler;

$handler = new GitLabHandler('webhook_secret');
if ($handler->validate()) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

### Travis CI
[Docs][travis-ci-webhook-docs] - [`TravisCIHandler.php`][travis-ci-handler-php]
```php
use NPM\ServiceWebhookHandler\Handlers\TravisCIHandler;

$handler = new TravisCIHandler();
if ($handler->validate()) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

### Telegram Login
[Docs][telegram-login-webhook-docs] - [`TelegramLoginHandler.php`][telegram-login-handler-php]
```php
use NPM\ServiceWebhookHandler\Handlers\TelegramLoginHandler;

$handler = new TelegramLoginHandler('123:BOT_API_KEY');
if ($handler->validate(json_encode($_GET))) {
    // All good, use the received data!
    $data = $handler->getData();
}
```

[github-swh]: https://github.com/noplanman/service-webhook-handler "Service Webhook Handler on GitHub"
[packagist-swh]: https://packagist.org/packages/noplanman/service-webhook-handler "Service Webhook Handler on Packagist"
[license]: https://github.com/noplanman/service-webhook-handler/blob/master/LICENSE "Service Webhook Handler license"

[latest-version-badge]: https://img.shields.io/packagist/v/noplanman/service-webhook-handler.svg
[min-php-version-badge]: https://img.shields.io/packagist/php-v/noplanman/service-webhook-handler.svg
[total-downloads-badge]: https://img.shields.io/packagist/dt/noplanman/service-webhook-handler.svg
[license-badge]: https://img.shields.io/packagist/l/noplanman/service-webhook-handler.svg
[composer]: https://getcomposer.org/ "Composer"

[github-webhook-docs]: https://developer.github.com/webhooks/
[github-handler-php]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/GitHubHandler.php
[gitlab-webhook-docs]: https://gitlab.com/gitlab-org/gitlab-ce/blob/master/doc/user/project/integrations/webhooks.md
[gitlab-handler-php]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/GitLabHandler.php
[travis-ci-webhook-docs]: https://docs.travis-ci.com/user/notifications/#Configuring-webhook-notifications
[travis-ci-handler-php]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/TravisCIHandler.php
[telegram-login-webhook-docs]: https://core.telegram.org/widgets/login
[telegram-login-handler-php]: https://github.com/noplanman/service-webhook-handler/blob/master/src/Handlers/TelegramLoginHandler.php
