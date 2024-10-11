<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Interfaces;

use PHPUnit\Framework\TestCase;

class EndpointInterfaceTest extends TestCase
{
    private $mockEndpoint;

    protected function setUp(): void
    {
        $this->mockEndpoint = $this->createMock(EndpointInterface::class);
    }

    public function testGetPath(): void
    {
        $language = 'en';
        $expectedPath = '/path/to/resource/en';

        $this->mockEndpoint->method('getPath')
            ->with($this->equalTo($language))
            ->willReturn($expectedPath);

        $this->assertEquals($expectedPath, $this->mockEndpoint->getPath($language));
    }

    public function testGetPathWithDifferentLanguage(): void
    {
        $language = 'fr';
        $expectedPath = '/path/to/resource/fr';

        $this->mockEndpoint->method('getPath')
            ->with($this->equalTo($language))
            ->willReturn($expectedPath);

        $this->assertEquals($expectedPath, $this->mockEndpoint->getPath($language));
    }
}
