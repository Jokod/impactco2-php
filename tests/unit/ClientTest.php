<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Unit;

use GuzzleHttp\ClientInterface;
use Jokod\Impactco2Php\Client;
use Jokod\Impactco2Php\Endpoints\Endpoint;
use Jokod\Impactco2Php\Enum\LanguagesEnum;
use Jokod\Impactco2Php\Exceptions\ApiException;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

/**
 * @covers \Jokod\Impactco2Php\Client
 */
class ClientTest extends TestCase
{
    private Client $client;
    /** @var MockObject|LoggerInterface */
    private $mockLogger;
    /** @var MockObject|ClientInterface */
    private $mockHttpClient;
    /** @var MockObject|ResponseInterface */
    private $mockResponse;
    /** @var MockObject|StreamInterface */
    private $mockStream;
    /** @var MockObject|Endpoint */
    private $mockEndpoint;

    protected function setUp(): void
    {
        $this->mockLogger = $this->createMock(LoggerInterface::class);
        $this->mockHttpClient = $this->createMock(ClientInterface::class);
        $this->mockResponse = $this->createMock(ResponseInterface::class);
        $this->mockStream = $this->createMock(StreamInterface::class);
        $this->mockEndpoint = $this->createMock(Endpoint::class);

        $this->client = new Client([
            'logger' => $this->mockLogger
        ]);
        $this->client->setHttpClient($this->mockHttpClient);
    }

    /**
     * @test
     */
    public function itShouldInitializeWithDefaultConfig(): void
    {
        $client = new Client();

        $this->assertNull($client->getApiKey());
        $this->assertSame(LanguagesEnum::default(), $client->getLanguage());
    }

    /**
     * @test
     */
    public function itShouldInitializeWithCustomConfig(): void
    {
        $config = [
            'api_key' => 'test_api_key',
            'language' => 'en',
        ];

        $client = new Client($config);

        $this->assertSame('test_api_key', $client->getApiKey());
        $this->assertSame('en', $client->getLanguage());
    }

    /**
     * @test
     */
    public function itShouldExecuteSuccessfulRequest(): void
    {
        $expectedResponse = ['data' => 'test'];

        $this->mockStream
            ->method('getContents')
            ->willReturn(json_encode($expectedResponse));

        $this->mockResponse
            ->method('getStatusCode')
            ->willReturn(200);

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->mockResponse);

        $result = $this->client->execute($this->mockEndpoint);

        $this->assertSame($expectedResponse, $result);
    }

    /**
     * @test
     */
    public function itShouldHandleFailedRequest(): void
    {
        $errorResponse = ['issues' => 'Test error'];

        $this->mockStream
            ->method('getContents')
            ->willReturn(json_encode($errorResponse));

        $this->mockResponse
            ->method('getStatusCode')
            ->willReturn(400);

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->mockResponse);

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Test error');

        $this->client->execute($this->mockEndpoint);
    }

    /**
     * @test
     */
    public function itShouldLogErrorOnFailure(): void
    {
        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with(
                'API request failed',
                $this->arrayHasKey('exception')
            );

        $this->mockStream
            ->method('getContents')
            ->willReturn(json_encode(['issues' => 'Test error']));

        $this->mockResponse
            ->method('getStatusCode')
            ->willReturn(500);

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->mockResponse);

        $this->expectException(ApiException::class);

        $this->client->execute($this->mockEndpoint);
    }

    /**
     * @test
     * @dataProvider provideInvalidLanguages
     */
    public function itShouldRejectInvalidLanguage(string $invalidLanguage): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->setLanguage($invalidLanguage);
    }

    public function provideInvalidLanguages(): array
    {
        return [
            'empty string' => [''],
            'invalid code' => ['xx'],
            'too long' => ['eng'],
        ];
    }

    /**
     * @test
     */
    public function testItShouldHandleNetworkError(): void
    {
        $this->mockHttpClient
            ->method('request')
            ->willThrowException(new \GuzzleHttp\Exception\ConnectException(
                'Network error',
                new \GuzzleHttp\Psr7\Request('GET', 'test')
            ));

        $this->mockLogger
            ->expects($this->once())
            ->method('error')
            ->with(
                'API request failed',
                $this->arrayHasKey('exception')
            );

        $this->expectException(ApiException::class);
        $this->expectExceptionMessage('Network error');

        $this->client->execute($this->mockEndpoint);
    }

    /**
     * @test
     */
    public function testItShouldHandleInvalidJsonResponse(): void
    {
        $this->mockStream
            ->method('getContents')
            ->willReturn('invalid json');

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->mockResponse);

        $this->expectException(ApiException::class);

        $this->client->execute($this->mockEndpoint);
    }

    public function testSetAndGetApiKey(): void
    {
        $apiKey = 'test_api_key';
        $this->client->setApiKey($apiKey);
        $this->assertSame($apiKey, $this->client->getApiKey());
    }

    public function testSetAndGetLanguage(): void
    {
        $language = 'en';
        $this->client->setLanguage($language);
        $this->assertSame($language, $this->client->getLanguage());
    }

    public function testSetAndGetLogger(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $this->client->setLogger($logger);
        $this->assertSame($logger, $this->client->getLogger());
    }

    public function testSetAndGetHttpClient(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->createMock(ClientInterface::class);
        $this->client->setHttpClient($httpClient);
        $this->assertSame($httpClient, $this->client->getHttpClient());
    }

    public function testCreateDefaultHttpClient(): void
    {
        $client = new Client();
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
    }

    public function testCreateDefaultLogger(): void
    {
        $client = new Client();
        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
    }

    public function testExecuteWithEmptyResponse(): void
    {
        $this->mockStream
            ->method('getContents')
            ->willReturn('');

        $this->mockResponse
            ->method('getStatusCode')
            ->willReturn(200);

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->mockResponse);

        $result = $this->client->execute($this->mockEndpoint);
        $this->assertSame([], $result);
    }

    public function testExecuteWithMalformedJsonResponse(): void
    {
        $this->mockStream
            ->method('getContents')
            ->willReturn('{invalid json}');

        $this->mockResponse
            ->method('getBody')
            ->willReturn($this->mockStream);

        $this->mockHttpClient
            ->method('request')
            ->willReturn($this->mockResponse);

        $this->expectException(ApiException::class);
        $this->client->execute($this->mockEndpoint);
    }

    public function testExecuteWithHttpClientError(): void
    {
        $this->mockHttpClient
            ->method('request')
            ->willThrowException(new \Exception('Network error'));

        $this->mockLogger
            ->expects($this->once())
            ->method('error');

        $this->expectException(ApiException::class);
        $this->client->execute($this->mockEndpoint);
    }

    public function testSetInvalidLanguage(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->client->setLanguage('invalid');
    }

    public function testCustomConfiguration(): void
    {
        $config = [
            'api_key' => 'test_key',
            'language' => 'en',
            'base_path' => 'https://custom.api.com/'
        ];

        $client = new Client($config);

        $this->assertSame('test_key', $client->getApiKey());
        $this->assertSame('en', $client->getLanguage());
    }

    /**
     * @test
     */
    public function testBuildOptionsWithApiKey(): void
    {
        $this->client->setApiKey('test_key');
        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->anything(),
                $this->callback(function ($options) {
                    return isset($options['headers']['Authorization'])
                        && $options['headers']['Authorization'] === 'Bearer test_key'
                        && $options['headers']['Accept'] === 'application/json'
                        && $options['headers']['Content-Type'] === 'application/json';
                })
            )
            ->willReturn($this->mockResponse);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('getContents')->willReturn('[]');

        $this->client->execute($this->mockEndpoint);
    }

    /**
     * @test
     */
    public function testBuildOptionsWithoutApiKey(): void
    {
        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->anything(),
                $this->callback(function ($options) {
                    return !isset($options['headers']['Authorization'])
                        && $options['headers']['Accept'] === 'application/json'
                        && $options['headers']['Content-Type'] === 'application/json';
                })
            )
            ->willReturn($this->mockResponse);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('getContents')->willReturn('[]');

        $this->client->execute($this->mockEndpoint);
    }

    /**
     * @test
     */
    public function testBuildOptionsWithCustomHeaders(): void
    {
        $this->client->setApiKey('test_key');
        $customOptions = [
            'headers' => ['Custom-Header' => 'test-value']
        ];

        $this->mockHttpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                $this->anything(),
                $this->callback(function ($options) {
                    return isset($options['headers']['Authorization'])
                        && $options['headers']['Authorization'] === 'Bearer test_key'
                        && $options['headers']['Custom-Header'] === 'test-value'
                        && $options['headers']['Accept'] === 'application/json'
                        && $options['headers']['Content-Type'] === 'application/json';
                })
            )
            ->willReturn($this->mockResponse);

        $this->mockResponse->method('getStatusCode')->willReturn(200);
        $this->mockResponse->method('getBody')->willReturn($this->mockStream);
        $this->mockStream->method('getContents')->willReturn('[]');

        $this->client->execute($this->mockEndpoint, $customOptions);
    }

    /**
     * @test
     */
    public function testGetLoggerCreatesDefaultLoggerWhenNotSet(): void
    {
        $client = new Client();
        $defaultLogger = $client->getLogger();
        $this->assertInstanceOf(LoggerInterface::class, $defaultLogger);

        /** @var LoggerInterface $newLogger */
        $newLogger = $this->createMock(LoggerInterface::class);
        $client->setLogger($newLogger);
        $this->assertSame($newLogger, $client->getLogger());
    }
}
