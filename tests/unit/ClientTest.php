<?php

declare(strict_types = 1);

use GuzzleHttp\ClientInterface;
use PHPUnit\Framework\TestCase;

class ClientTest extends TestCase
{
    public function testGetLibraryVersion(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $this->assertEquals('1.0.0', $client->getLibraryVersion());
    }

    public function testSetAndGetApiKey(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $client->setApiKey('test_api_key');
        $this->assertEquals('test_api_key', $client->getApiKey());
    }

    public function testSetAndGetLanguage(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $client->setLanguage('en');
        $this->assertEquals('en', $client->getLanguage());
    }

    public function testSetInvalidLanguage(): void
    {
        $this->expectException(\Exception::class);
        $client = new \Jokod\Impactco2Php\Client();
        $client->setLanguage('invalid_language');
    }

    public function testSetAndGetConfig(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $client->setConfig('test_key', 'test_value');
        $this->assertEquals('test_value', $client->getConfig('test_key'));
    }

    public function testGetConfigWithDefault(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $this->assertEquals('default_value', $client->getConfig('non_existent_key', 'default_value'));
    }

    public function testSetAndGetHttpClient(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        /** @var ClientInterface $mockHttpClient */
        $mockHttpClient = $this->createMock(ClientInterface::class);
        $client->setHttpClient($mockHttpClient);
        $this->assertSame($mockHttpClient, $client->getHttpClient());
    }

    public function testGetDefaultHttpClient(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $this->assertInstanceOf(\GuzzleHttp\Client::class, $client->getHttpClient());
    }

    public function testSetAndGetLogger(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        /** @var \Psr\Log\LoggerInterface $mockLogger */
        $mockLogger = $this->createMock(\Psr\Log\LoggerInterface::class);
        $client->setLogger($mockLogger);
        $this->assertSame($mockLogger, $client->getLogger());
    }

    public function testGetDefaultLogger(): void
    {
        $client = new \Jokod\Impactco2Php\Client();
        $this->assertInstanceOf(\Monolog\Logger::class, $client->getLogger());
    }
}
