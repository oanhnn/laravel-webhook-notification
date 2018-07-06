<?php

namespace Laravel\WebhookNotification;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Notifications\Notifiable;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Log;
use Laravel\WebhookNotification\Exceptions\CouldNotSendException;
use Laravel\WebhookNotification\Exceptions\SendFailedException;
use Laravel\WebhookNotification\Exceptions\WebhookNotificationException;

class WebhookChannel
{
    const RESPONSE_STATUS_SUCCESS = 200;

    /**
     * @var ClientInterface
     */
    private $client;

    /**
     * WebhookChannel constructor.
     * @param ClientInterface $client
     */
    public function __construct(ClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * @param Notifiable $notifiable
     * @param Notification $notification
     * @throws WebhookNotificationException
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$notifiable instanceof WebhookNotifiable) {
            throw new CouldNotSendException('Only send webhook to a webhook notifiable');
        }

        if (method_exists($notification, 'toWebhook')) {
            $data = $notification->toWebhook($notifiable);
        } else {
            $data = $notification->toArray($notifiable);
        }

        if (!$data instanceof WebhookMessage) {
            $data = WebhookMessage::create($data);
        }

        $request = $data->signWith($notifiable->getSigningKey())->toRequest($notifiable->getWebhookUrl());

        try {
            $response = $this->client->send($request);
            if ($response->getStatusCode() !== self::RESPONSE_STATUS_SUCCESS) {
                throw new SendFailedException($response);
            }
            // Log successfully
            Log::debug('Webhook successfully posted to ' . $notifiable->getWebhookUrl());
            return;
        } catch (BadResponseException $exception) {
            throw new SendFailedException($exception->getResponse(), $exception);
        } catch (GuzzleException $exception) {
            throw new CouldNotSendException($exception->getMessage(), $exception->getCode(), $exception);
        }

        Log::error('Webhook failed in posting to ' . $notifiable->getWebhookUrl());
    }
}
