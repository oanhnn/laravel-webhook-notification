<?php

namespace Laravel\WebhookNotification\Tests;

use Illuminate\Notifications\Notification;

class BarNotification extends Notification
{
    /**
     * @param $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'payload' => [
                'type' => 'bar',
            ],
        ];
    }
}
