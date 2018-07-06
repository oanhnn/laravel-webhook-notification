<?php

namespace Laravel\WebhookNotification\Tests\Unit;

use GuzzleHttp\Psr7\Response;
use Laravel\WebhookNotification\Exceptions\SendFailedException;
use Orchestra\Testbench\TestCase;

class SendFailedExceptionTest extends TestCase
{
    /** @test */
    public function it_can_be_created_with_response()
    {
        $response = new Response(400);
        $exception = new SendFailedException($response);

        $this->assertSame($response, $exception->getResponse());
    }
}
