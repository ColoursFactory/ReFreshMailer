<?php

namespace Tests;

use ColoursFactory\ReFreshMailer\Http\HttpAdapterInterface;
use ColoursFactory\ReFreshMailer\FreshMailClient;
use PHPUnit\Framework\TestCase;

class FreshMailClientTest extends TestCase
{
    /** @var HttpAdapterInterface|\PHPUnit_Framework_MockObject_MockObject */
    private $httpAdapterMock;

    /** @var FreshMailClient */
    private $freshMailClient;

    public function setUp()
    {
        $this->httpAdapterMock = $this->createMock(HttpAdapterInterface::class);
        $this->freshMailClient = new FreshMailClient($this->httpAdapterMock, 'http://api.test.com', [
            'apiKey'    => 'ABC',
            'apiSecret' => '123',
        ]);

        parent::setUp();
    }

    /**
     * @test
     */
    public function signatureIsCalculatedProperly()
    {
        $this
            ->httpAdapterMock
            ->expects(self::once())
            ->method('calculateSignature')
            ->willReturn('ABC123');

        $this->freshMailClient->doRequest('/test', []);
    }
}