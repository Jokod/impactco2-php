<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Endpoints;

use Jokod\Impactco2Php\Enum\ThematicEnum;
use Jokod\Impactco2Php\Exceptions\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class ThematicsEcvEndpointTest extends TestCase
{
    public function testConstructorWithValidIdAndDefaultDetail(): void
    {
        $id = ThematicEnum::CLOTHING;
        $endpoint = new ThematicsEcvEndpoint($id);
        $this->assertInstanceOf(ThematicsEcvEndpoint::class, $endpoint);
    }

    public function testConstructorWithValidIdAndDetailOne(): void
    {
        $id = ThematicEnum::DRINK;
        $detail = 1;
        $endpoint = new ThematicsEcvEndpoint($id, $detail);
        $this->assertInstanceOf(ThematicsEcvEndpoint::class, $endpoint);
    }

    public function testConstructorWithInvalidId(): void
    {
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid thematic ECV identifier');
        new ThematicsEcvEndpoint(-1);
    }

    public function testConstructorWithInvalidDetail(): void
    {
        $id = ThematicEnum::FRUITS_AND_VEGETABLES;
        $this->expectException(InvalidArgumentException::class);
        $this->expectExceptionMessage('Detail must be 0 or 1');
        new ThematicsEcvEndpoint($id, 2);
    }
}
