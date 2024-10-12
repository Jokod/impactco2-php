<?php

namespace Jokod\Impactco2Php\Tests\Unit;

use GuzzleHttp\ClientInterface;
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
    public function testConstructorWithDefaultConfig()
    {
        $client = new Client([]);
        $this->assertSame(Client::API_BASE_PATH, $client->getConfig('base_path'));
        $this->assertNull($client->getApiKey());
        $this->assertSame(LanguagesEnum::default(), $client->getLanguage());
    }

    public function testConstructorWithCustomConfig()
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

    public function testSetAndGetApiKey()
    {
        $client = new Client();
        $client->setApiKey('test_api_key');
        $this->assertSame('test_api_key', $client->getApiKey());
    }

    public function testSetAndGetLanguage()
    {
        $client = new Client();
        $client->setLanguage('en');
        $this->assertSame('en', $client->getLanguage());
    }

    public function testSetInvalidLanguage()
    {
        $this->expectException(Exception::class);
        $client = new Client();
        $client->setLanguage('invalid_language');
    }

    public function testSetAndGetConfig()
    {
        $client = new Client();
        $client->setConfig('test_key', 'test_value');
        $this->assertSame('test_value', $client->getConfig('test_key'));
    }

    public function testSetEmptyConfigKey()
    {
        $this->expectException(InvalidArgumentException::class);
        $client = new Client();
        $client->setConfig('', 'test_value');
    }

    public function testLoggerIsSetAndUnset()
    {
        $config = ['logger' => $this->createMock(LoggerInterface::class)];
        $client = new Client($config);

        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
    }

    public function testSetAndGetHttpClient()
    {
        /** @var ClientInterface $httpClient */
        $httpClient = $this->createMock(ClientInterface::class);
        $client = new Client();
        $client->setHttpClient($httpClient);
        $this->assertSame($httpClient, $client->getHttpClient());
    }

    public function testGetDefaultHttpClient()
    {
        $client = new Client();
        $this->assertInstanceOf(ClientInterface::class, $client->getHttpClient());
    }

    public function testSetAndGetLogger()
    {
        /** @var LoggerInterface $logger */
        $logger = $this->createMock(LoggerInterface::class);
        $client = new Client();
        $client->setLogger($logger);
        $this->assertSame($logger, $client->getLogger());
    }

    public function testGetDefaultLogger()
    {
        $client = new Client();
        $this->assertInstanceOf(LoggerInterface::class, $client->getLogger());
    }

    public function testExecuteSuccess()
    {
        $endpoint = $this->createMock(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(json_encode(['data' => 'test']));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(200);
        $response->method('getBody')->willReturn($responseBody);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        /** @var Endpoint $endpoint */
        $result = $client->execute($endpoint);
        $this->assertSame(['data' => 'test'], $result);
    }

    public function testExecuteError()
    {
        $endpoint = $this->createMock(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(json_encode(['issues' => 'Unknown error']));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getStatusCode')->willReturn(500);
        $response->method('getBody')->willReturn($responseBody);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown error');
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testExecuteUnknownError()
    {
        $endpoint = $this->createMock(Endpoint::class);
        $endpoint->method('getPath')->willReturn('test_path');

        $responseBody = $this->createMock(StreamInterface::class);
        $responseBody->method('getContents')->willReturn(\json_encode(['test' => 'test']));

        $response = $this->createMock(ResponseInterface::class);
        $response->method('getBody')->willReturn($responseBody);
        $response->method('getStatusCode')->willReturn(500);

        $httpClient = $this->createMock(ClientInterface::class);
        $httpClient->method('request')->willReturn($response);

        $client = new Client();
        /** @var ClientInterface $httpClient */
        $client->setHttpClient($httpClient);

        $this->expectException(Exception::class);
        $this->expectExceptionMessage('Unknown error');
        /** @var Endpoint $endpoint */
        $client->execute($endpoint);
    }

    public function testAddOptions()
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

    public function testAddOptionsWithApiKey()
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
