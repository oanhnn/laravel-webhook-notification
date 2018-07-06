<?php

namespace Laravel\WebhookNotification\Tests;

use Illuminate\Notifications\Notification;

class BazNotification extends Notification
{
    /**
     * @param $notifiable
     * @return array
     */
    public function toWebhook($notifiable)
    {
        return [
            'payload' => [
                'type' => 'bar',
            ],
        ];
    }
}
