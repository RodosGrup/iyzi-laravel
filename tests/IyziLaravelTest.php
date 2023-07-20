<?php

namespace Rodosgrup\IyziLaravel\Tests;

use Rodosgrup\IyziLaravel\IyziLaravel;
use PHPUnit\Framework\TestCase;

class IyziLaravelTest extends TestCase
{
    /** @test iyzi-laravel */
    public function it_returns_a_iyzi_laravel()
    {
        $iyzi = new IyziLaravel();
        $iyzi->hiyziLaravel();

        $this->assertSame('iyzi', $iyzi);
    }
}
