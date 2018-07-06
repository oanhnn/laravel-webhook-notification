# Introduction

[![Build Status](https://travis-ci.org/oanhnn/laravel-webhook-notification.svg?branch=master)](https://travis-ci.org/oanhnn/laravel-webhook-notification)
[![Coverage Status](https://coveralls.io/repos/github/oanhnn/laravel-webhook-notification/badge.svg?branch=master)](https://coveralls.io/github/oanhnn/laravel-webhook-notification?branch=master)

Easy send Webhook notification in Laravel 5.5+ Application

## Requirements

* php >=7.1.3
* Laravel 5.5+

## Installation

Begin by pulling in the package through Composer.

```bash
$ composer require oanhnn/laravel-webhook-notification
```

## Usage

Implement webhook notifiable:

```php

class User extends Authenticatable implements WebhookNotifiable
{
    use Notifiable;

    /**
     * @return string
     */
    public function getSigningKey(): string
    {
        return $this->api_key;
    }

    /**
     * @return string
     */
    public function getWebhookUrl(): string
    {
        return $this->webhook_url;
    }
}
```

In notification class,

```php

class ProjectCreated extends Notification
{
    /**
     * @return array
     */
    public function via($notifiable)
    {
        return [WebhookChannel::class];
    }

    /**
     * @return array|WebhookMessage
     */
    public function toWebhook($notifiable)
    {
        return WebhookMessage::create()
            ->data([
               'payload' => [
                   'foo' => 'bar'
               ]
            ])
            ->userAgent("Custom-User-Agent")
            ->header('X-Custom', 'Custom-Header');
    }
}

```

See more in [Laravel document](https://laravel.com/docs/5.6/notification)

## Changelog

See all change logs in [CHANGELOG](CHANGELOG.md)

## Testing

```bash
$ git clone git@github.com/oanhnn/laravel-webhook-notification.git /path
$ cd /path
$ composer install
$ composer phpunit
```

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Security

If you discover any security related issues, please email to [Oanh Nguyen](mailto:oanhnn.bk@gmail.com) instead of 
using the issue tracker.

## Credits

- [Oanh Nguyen](https://github.com/oanhnn)
- [All Contributors](../../contributors)

## License

This project is released under the MIT License.   
Copyright © 2018 [Oanh Nguyen](https://oanhnn.github.io/).
