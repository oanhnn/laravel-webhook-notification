<?php

namespace Laravel\WebhookNotification\Tests\Unit;

use Laravel\WebhookNotification\Tests\NonPublicAccessibleTrait;
use Laravel\WebhookNotification\WebhookMessage;
use Orchestra\Testbench\TestCase;
use Psr\Http\Message\RequestInterface;

class WebhookMessageTest extends TestCase
{
    use NonPublicAccessibleTrait;

    /** @test */
    public function it_can_create_without_data()
    {
        $message = WebhookMessage::create();

        $this->assertInstanceOf(WebhookMessage::class, $message);
        $this->assertNull($this->getNonPublicProperty($message, 'data'));
    }

    /** @test */
    public function it_can_create_with_data()
    {
        $message = WebhookMessage::create(['foo' => 'bar']);
        $data = $this->getNonPublicProperty($message, 'data');

        $this->assertInstanceOf(WebhookMessage::class, $message);
        $this->assertTrue(is_array($data));
        $this->assertArrayHasKey('foo', $data);
        $this->assertJsonStringEqualsJsonString('{"foo":"bar"}', json_encode($data));
    }

    /** @test */
    public function it_can_set_data()
    {
        $message = WebhookMessage::create();

        // Before set data, it should be NULL
        $this->assertNull($this->getNonPublicProperty($message, 'data'));

        $message->data(['foo' => 'bar']);
        $data = $this->getNonPublicProperty($message, 'data');

        // After set data, it should be set
        $this->assertTrue(is_array($data));
        $this->assertArrayHasKey('foo', $data);
        $this->assertJsonStringEqualsJsonString('{"foo":"bar"}', json_encode($data));
    }

    /** @test */
    public function it_can_replace_data()
    {
        $message = WebhookMessage::create(['foo' => 'bar']);
        $data1 = $this->getNonPublicProperty($message, 'data');

        $this->assertTrue(is_array($data1));
        $this->assertJsonStringEqualsJsonString('{"foo":"bar"}', json_encode($data1));

        $message->data(['foo' => 'baz', 'abc' => 'xyz']);
        $data2 = $this->getNonPublicProperty($message, 'data');

        $this->assertTrue(is_array($data2));
        $this->assertJsonStringEqualsJsonString('{"foo":"baz","abc":"xyz"}', json_encode($data2));
    }

    /** @test */
    public function it_can_add_header()
    {
        $message = WebhookMessage::create(['foo' => 'bar']);

        $this->assertArrayNotHasKey('Custom', $this->getNonPublicProperty($message, 'headers'));

        $message->header('Custom', 'value');
        $this->assertArrayHasKey('Custom', $this->getNonPublicProperty($message, 'headers'));
        $this->assertSame('value', $this->getNonPublicProperty($message, 'headers')['Custom']);
    }

    /** @test */
    public function it_can_override_header()
    {
        $message = WebhookMessage::create(['foo' => 'bar']);

        $message->header('Custom', 'value1');
        $this->assertArrayHasKey('Custom', $this->getNonPublicProperty($message, 'headers'));
        $this->assertSame('value1', $this->getNonPublicProperty($message, 'headers')['Custom']);

        $message->header('Custom', 'value2');
        $this->assertArrayHasKey('Custom', $this->getNonPublicProperty($message, 'headers'));
        $this->assertSame('value2', $this->getNonPublicProperty($message, 'headers')['Custom']);
    }

    /** @test */
    public function it_can_set_user_agent()
    {
        $message = WebhookMessage::create(['foo' => 'bar']);

        $this->assertArrayNotHasKey('User-Agent', $this->getNonPublicProperty($message, 'headers'));

        $message->userAgent('Chrome');
        $this->assertArrayHasKey('User-Agent', $this->getNonPublicProperty($message, 'headers'));
        $this->assertSame('Chrome', $this->getNonPublicProperty($message, 'headers')['User-Agent']);
    }

    /** @test */
    public function it_can_set_signing_key()
    {
        $message = WebhookMessage::create(['foo' => 'bar']);

        $this->assertArrayNotHasKey('User-Agent', $this->getNonPublicProperty($message, 'headers'));

        $message->userAgent('Chrome');
        $this->assertArrayHasKey('User-Agent', $this->getNonPublicProperty($message, 'headers'));
        $this->assertSame('Chrome', $this->getNonPublicProperty($message, 'headers')['User-Agent']);
    }

    /** @test */
    public function it_can_convert_to_request()
    {
        $request = WebhookMessage::create()
            ->data(['foo' => 'bar'])
            ->header('Custom', 'value')
            ->userAgent('Chrome')
            ->signWith('key')
            ->toRequest('https://notifiable-webhook-url.com');

        $this->assertInstanceOf(RequestInterface::class, $request);
        $this->assertSame('{"foo":"bar"}', $request->getBody()->getContents());
        $this->assertArrayHasKey('Custom', $request->getHeaders());
        $this->assertArrayHasKey('timestamp', $request->getHeaders());
        $this->assertArrayHasKey('signature', $request->getHeaders());
        $this->assertSame('https://notifiable-webhook-url.com', (string)$request->getUri());
    }
}
