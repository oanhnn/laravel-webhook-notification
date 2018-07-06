<?php

namespace Laravel\WebhookNotification\Tests;

use Illuminate\Notifications\Notifiable;
use Laravel\WebhookNotification\WebhookNotifiable;

class TestNotifiable implements WebhookNotifiable
{
    use Notifiable;

    /**
     * @return string
     */
    public function getSigningKey(): string
    {
        return 'signingKey123456789';
    }

    /**
     * @return string
     */
    public function getWebhookUrl(): string
    {
        return 'https://notifiable-webhook-url.com';
    }
}
