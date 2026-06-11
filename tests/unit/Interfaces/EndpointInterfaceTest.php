<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Interfaces;

use PHPUnit\Framework\TestCase;

class EndpointInterfaceTest extends TestCase
{
    public function testGetPath(): void
    {
        $language = 'en';
        $expectedPath = '/path/to/resource/en';

        $endpoint = $this->createMock(EndpointInterface::class);
        $endpoint->expects($this->once())
            ->method('getPath')
            ->with($this->equalTo($language))
            ->willReturn($expectedPath);

        $this->assertSame($expectedPath, $endpoint->getPath($language));
    }

    public function testGetPathWithDifferentLanguage(): void
    {
        $language = 'fr';
        $expectedPath = '/path/to/resource/fr';

        $endpoint = $this->createMock(EndpointInterface::class);
        $endpoint->expects($this->once())
            ->method('getPath')
            ->with($this->equalTo($language))
            ->willReturn($expectedPath);

        $this->assertSame($expectedPath, $endpoint->getPath($language));
    }
}
