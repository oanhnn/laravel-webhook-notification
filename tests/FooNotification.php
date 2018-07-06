<?php

namespace Laravel\WebhookNotification\Tests;

use Illuminate\Notifications\Notification;
use Laravel\WebhookNotification\WebhookMessage;

class FooNotification extends Notification
{
    /**
     * @param $notifiable
     * @return WebhookMessage|array
     */
    public function toWebhook($notifiable)
    {
        return WebhookMessage::create()
            ->data([
                'payload' => [
                    'type' => 'foo',
                ],
            ])
            ->userAgent('WebhookAgent')
            ->header('X-Custom', 'CustomHeader');
    }
}
