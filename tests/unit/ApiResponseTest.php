<?php

declare(strict_types = 1);

namespace Jokod\Impactco2Php\Tests\Unit;

use Jokod\Impactco2Php\ApiResponse;
use PHPUnit\Framework\TestCase;

class ApiResponseTest extends TestCase
{
    public function testGetDataAndWarning(): void
    {
        $data = ['id' => 1, 'name' => 'test'];
        $warning = 'Avertissement';
        $response = new ApiResponse($data, $warning);

        $this->assertSame($data, $response->getData());
        $this->assertSame($warning, $response->getWarning());
    }

    public function testGetWarningNullByDefault(): void
    {
        $response = new ApiResponse([]);
        $this->assertNull($response->getWarning());
    }
}
