<?php

namespace Laravel\WebhookNotification\Tests\Unit;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ConnectException;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Laravel\WebhookNotification\Exceptions\CouldNotSendException;
use Laravel\WebhookNotification\Exceptions\SendFailedException;
use Laravel\WebhookNotification\Tests\BarNotification;
use Laravel\WebhookNotification\Tests\BazNotification;
use Laravel\WebhookNotification\Tests\NonWebhookNotifiable;
use Laravel\WebhookNotification\Tests\TestNotifiable;
use Laravel\WebhookNotification\Tests\FooNotification;
use Laravel\WebhookNotification\WebhookChannel;
use Mockery;
use Orchestra\Testbench\TestCase;

class WebhookChannelTest extends TestCase
{
    /** @test */
    public function it_can_send_a_notification()
    {
        $response = new Response(200);
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andReturn($response);

        $channel = new WebhookChannel($client);
        $channel->send(new TestNotifiable(), new FooNotification());
    }

    /** @test */
    public function it_can_send_a_notification_by_to_array()
    {
        $response = new Response(200);
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andReturn($response);

        $channel = new WebhookChannel($client);
        $channel->send(new TestNotifiable(), new BarNotification());
    }

    /** @test */
    public function it_can_send_a_notification_by_to_webhook()
    {
        $response = new Response(200);
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andReturn($response);

        $channel = new WebhookChannel($client);
        $channel->send(new TestNotifiable(), new BazNotification());
    }

    /** @test */
    public function it_throws_an_exception_when_notifiable_is_non_webhook_notifiable()
    {
        $client = Mockery::mock(ClientInterface::class);

        $this->expectException(CouldNotSendException::class);

        $channel = new WebhookChannel($client);
        $channel->send(new NonWebhookNotifiable(), new FooNotification());
    }

    /** @test */
    public function it_throws_an_exception_when_response_is_unsuccessful()
    {
        $response = new Response(500);
        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andReturn($response);

        $this->expectException(SendFailedException::class);

        $channel = new WebhookChannel($client);
        $channel->send(new TestNotifiable(), new FooNotification());
    }

    /** @test */
    public function it_throw_an_exception_when_client_send_the_notification_failed()
    {
        $request = new Request('POST', 'https://notifiable-webhook-url.com', [], '{"payload:{"type":"foo"}"}');
        $response = new Response(400);

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andThrow(new ClientException('Bad request', $request, $response));

        $this->expectException(SendFailedException::class);

        $channel = new WebhookChannel($client);
        $channel->send(new TestNotifiable(), new FooNotification());
    }

    /** @test */
    public function it_throw_an_exception_when_client_could_not_send_the_notification()
    {
        $request = new Request('POST', 'https://notifiable-webhook-url.com', [], '{"payload:{"type":"foo"}"}');

        $client = Mockery::mock(ClientInterface::class);
        $client->shouldReceive('send')
            ->once()
            ->andThrow(new ConnectException('Connection time out', $request));

        $this->expectException(CouldNotSendException::class);

        $channel = new WebhookChannel($client);
        $channel->send(new TestNotifiable(), new FooNotification());
    }
}
