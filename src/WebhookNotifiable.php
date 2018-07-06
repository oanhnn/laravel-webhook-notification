<?php

namespace Laravel\WebhookNotification;

interface WebhookNotifiable
{
    /**
     * @return string
     */
    public function getSigningKey(): string;

    /**
     * @return string
     */
    public function getWebhookUrl(): string;
}
