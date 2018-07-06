<?php

namespace Laravel\WebhookNotification;

use GuzzleHttp\Psr7\Request;
use Psr\Http\Message\RequestInterface;

class WebhookMessage
{
    /**
     * Message data
     *
     * @var mixed
     */
    protected $data;

    /**
     * Message headers
     *
     * @var array
     */
    protected $headers = [];

    /**
     * Client user agent
     *
     * @var string|null
     */
    protected $userAgent;

    /**
     * Key for signing
     *
     * @var string
     */
    protected $key;

    /**
     * @param mixed $data
     * @return WebhookMessage
     */
    public static function create($data = null)
    {
        return (new static())->data($data);
    }

    /**
     * Set the Webhook data to be JSON encoded.
     *
     * @param mixed $data
     * @return WebhookMessage
     */
    public function data($data)
    {
        $this->data = $data;

        return $this;
    }

    /**
     * Add a Webhook request custom header.
     *
     * @param string $name
     * @param string $value
     * @return WebhookMessage
     */
    public function header(string $name, $value)
    {
        $this->headers[$name] = $value;

        return $this;
    }

    /**
     * Set the Webhook request UserAgent.
     *
     * @param string $userAgent
     * @return WebhookMessage
     */
    public function userAgent($userAgent)
    {
        $this->headers['User-Agent'] = $userAgent;

        return $this;
    }

    /**
     * @param string $key
     * @return WebhookMessage
     */
    public function signWith(string $key)
    {
        $this->key = $key;

        return $this;
    }

    /**
     * @param \Psr\Http\Message\UriInterface|string
     * @return \Psr\Http\Message\RequestInterface
     */
    public function toRequest($uri): RequestInterface
    {
        $body = json_encode($this->data);
        $headers = $this->headers;

        if ($this->key) {
            $timestamp = now()->getTimestamp();
            $signature = hash_hmac('sha256', $body . $timestamp, $this->key);
            $headers = array_merge($headers, compact('timestamp', 'signature'));
        }

        return new Request('POST', $uri, $headers, $body);
    }
}
