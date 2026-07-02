<?php

namespace Jokod\Impactco2Php\Tests\Unit;

use GuzzleHttp\ClientInterface;
use Jokod\Impactco2Php\ApiResponse;
use Jokod\Impactco2Php\Client;
use Jokod\Impactco2Php\Endpoints\Endpoint;
use Jokod\Impactco2Php\Enum\LanguagesEnum;
use Jokod\Impactco2Php\Exceptions\Exception;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Psr\Log\LoggerInterface;

class ClientTest extends TestCase
{
    public function testConstructorWithDefaultConfig(): void
    {
        $client = new Client([]);
        $this->assertSame(Client::API_BASE_PATH, $client->getConfig('base_path'));
        $this->assertNull($client->getApiKey());
        $this->assertSame(LanguagesEnum::default(), $client->getLanguage());
    }

    public function testConstructorWithCustomConfig(): void
    {
        $config = [
            'api_key'  => 'test_api_key',
            'language' => 'en',
        ];
        $client = new Client($config);
        $this->assertSame('test_api_key', $client->getApiKey());
        $this->assertSame('en', $client->getLanguage());
        $this->assertInstanceOf(\GuzzleHttp\Client::class, $client->getHttpClient());
        $this->assertInstanceOf(\Monolog\Logger::class, $client->getLogger());
    }

    public function testSetAndGetApiKey(): void
    {
        $client = new Client();
        $client->setApiKey('test_api_key');
        $this->assertSame('test_api_key', $client->getApiKey());
    }

    public function testSetAndGetLanguage(): void
    {
        $client = new Client();
        $client->setLanguage('en');
        $this->assertSame('en', $client->getLanguage());
    }

    public function testSetInvalidLanguage(): void
    {
        $this->expectException(Exception::class);
        $client = new Client();
        $client->setLanguage('invalid_language');
    }

    public function testSetAndGetConfig(): void
    {
        $client = new Client();
        $client->setConfig('test_key', 'test_value');
        $this->assertSame('test_value', $client->getConfig('test_key'));
    }

    public function testSetEmptyConfigKey(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new Client();
        $client->setConfig('', 'test_value');
    }

    public function testSetInvalidBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base_path: must use HTTPS and point to impactco2.fr');
        $client = new Client();
        $client->setConfig('base_path', 'http://evil.com/api/v1/');
    }

    public function testConstructorWithInvalidBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        new Client(['base_path' => 'https://example.com/api/v1/']);
    }

    public function testSetEmptyBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base_path: must be a non-empty HTTPS URL');
        $client = new Client();
        $client->setConfig('base_path', '');
    }

    public function testSetNonStringBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base_path: must be a non-empty HTTPS URL');
        $client = new Client();
        $client->setConfig('base_path', 123);
    }

    public function testConstructorWithEmptyBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base_path: must be a non-empty HTTPS URL');
        new Client(['base_path' => '']);
    }

    public function testConstructorWithNonStringBasePath(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid base_path: must be a non-empty HTTPS URL');
        new Client(['base_path' => null]);
    }

    public function testSetValidBasePath(): void
    {
        $client = new Client();
        $validPath = 'https://impactco2.fr/api/v1/';
        $client->setConfig('base_path', $validPath);
        $this->assertSame($validPath, $client->getConfig('base_path'));
    }

    public function testGetConfigReturnsNullForUnknownKey(): void
    {
        $client = new Client();
        $this->assertNull($client->getConfig('unknown_key'));
    }

    public function testExecuteWithInvalidJsonResponse(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('transport?km=100');
        $endpoint->method('transformResponse')->willReturnCallback(
            static fn (array $raw): ApiResponse => new ApiResponse($raw['data'] ?? [], null)
        );

        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn('not-json');

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseBody);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        $client->setHttpClient($httpClient);

        /** @var Endpoint $endpoint */
        $result = $client->execute($endpoint);
        $this->assertSame([], $result->getData());
    }

    public function testExecuteBuildsPathWithLanguage(): void
    {
        $endpoint = $this->createMock(Endpoint::class);
        $endpoint->expects($this->once())
            ->method('getPath')
            ->with('es')
            ->willReturn('transport?km=100&language=es');
        $endpoint->method('transformResponse')->willReturn(new ApiResponse([], null));

        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(json_encode(['data' => []]));

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseBody);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->expects($this->once())
            ->method('request')
            ->with(
                'GET',
                Client::API_BASE_PATH . 'transport?km=100&language=es',
                $this->anything()
            )
            ->willReturn($response);

        $client = new Client(['language' => 'es']);
        $client->setHttpClient($httpClient);

        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testAddOptionsMergesCustomHeaders(): void
    {
        $client = new Client();
        $result = $client->addOptions([
            'headers' => [
                'X-Custom' => 'value',
            ],
        ]);

        $this->assertSame('value', $result['headers']['X-Custom']);
        $this->assertSame('application/json', $result['headers']['Accept']);
    }

    public function testExecuteLogsOptionsWithoutAuthorizationWhenNoApiKey(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willThrowException(new \RuntimeException('Connection refused'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                'Error during request',
                $this->callback(static function (array $context): bool {
                    return !isset($context['options']['headers']['Authorization']);
                })
            );

        $client = new Client(['logger' => $logger]);
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testDefaultHttpClientUsesConfiguredBasePath(): void
    {
        $client = new Client();
        $httpClient = $client->getHttpClient();
        $this->assertInstanceOf(\GuzzleHttp\Client::class, $httpClient);

        $config = $httpClient->getConfig();
        $this->assertSame(Client::API_BASE_PATH, (string) $config['base_uri']);
        $this->assertFalse($config['http_errors']);
        $this->assertSame(30, $config['timeout']);
        $this->assertSame(10, $config['connect_timeout']);
    }

    public function testApiConstants(): void
    {
        $this->assertSame('v1', Client::API_VERSION);
        $this->assertSame('https://impactco2.fr/api/v1/', Client::API_BASE_PATH);
    }

    public function testExecuteNetworkErrorReturnsGenericMessage(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willThrowException(new \RuntimeException('Connection refused'));

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unable to connect to Impact CO2 API');
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testExecuteLogsSanitizedOptionsOnError(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willThrowException(new \RuntimeException('Connection refused'));

        $logger = $this->createMock(LoggerInterface::class);
        $logger->expects($this->once())
            ->method('error')
            ->with(
                'Error during request',
                $this->callback(static function (array $context): bool {
                    return ($context['options']['headers']['Authorization'] ?? null) === 'Bearer ***';
                })
            );

        $client = new Client(['api_key' => 'secret-key', 'logger' => $logger]);
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testLoggerIsSetAndUnset(): void
    {
        $config = ['logger' => $this->createStub(LoggerInterface::class)];
        $client = new Client($config);

        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
    }

    public function testSetAndGetHttpClient(): void
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->createStub(ClientInterface::class);
        $client = new Client();
        $client->setHttpClient($httpClient);
        $this->assertSame($httpClient, $client->getHttpClient());
    }

    public function testGetDefaultHttpClient(): void
    {
        $client = new Client();
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
    }

    public function testSetAndGetLogger(): void
    {
        /** @var LoggerInterface $logger */
        $logger = $this->createStub(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);
        $this->assertSame($logger, $client->getLogger());
    }

    public function testGetDefaultLogger(): void
    {
        $client = new Client();
        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
    }

    public function testExecuteSuccess(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');
        $endpoint->method('transformResponse')->willReturnCallback(
            static fn (array $raw): ApiResponse => new ApiResponse($raw['data'] ?? null, $raw['warning'] ?? null)
        );

        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(json_encode(['data' => 'test']));

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseBody);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        /** @var Endpoint $endpoint */
        $result = $client->execute($endpoint);
        $this->assertInstanceOf(ApiResponse::class, $result);
        $this->assertSame('test', $result->getData());
        $this->assertNull($result->getWarning());
    }

    public function testExecuteError(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(json_encode(['issues' => 'Unknown error']));

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);
        $response->method('getBody')->willReturn($responseBody);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown error');
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testExecuteUnknownError(): void
    {
        $endpoint = $this->createStub(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $responseBody = $this->createStub(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(\json_encode(['test' => 'test']));

        $response = $this->createStub(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);
        $response->method('getStatusCode')->willReturn(500);

        $httpClient = $this->createStub(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown error');
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testAddOptions(): void
    {
        $client = new Client();
        $result = $client->addOptions([]);
        $expected = [
            'headers' => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ];
        $this->assertSame($expected, $result);
        $this->assertArrayHasKey('headers', $result);
    }

    public function testAddOptionsWithApiKey(): void
    {
        $client = new Client([
            'api_key' => 'testApiKey',
        ]);
        $result = $client->addOptions([]);
        $expected = [
            'headers' => [
                'Accept'        => 'application/json',
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer testApiKey',
            ],
        ];
        $this->assertSame($expected, $result);
        $this->assertArrayHasKey('headers', $result);
    }
}
