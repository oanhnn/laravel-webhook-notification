<?php

namespace Laravel\WebhookNotification\Exceptions;

use Psr\Http\Message\ResponseInterface;

class SendFailedException extends \RuntimeException implements WebhookNotificationException
{
    /**
     * @var ResponseInterface
     */
    protected $response;

    /**
     * Construct the exception.
     */
    public function __construct(ResponseInterface $response, \Throwable $previous = null)
    {
        parent::__construct(
            'Webhook responded with an error: `' . $response->getBody()->getContents() . '`',
            0,
            $previous
        );

        $this->response = $response;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }
}
