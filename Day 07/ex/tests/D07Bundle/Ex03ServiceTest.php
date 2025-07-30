<?php

namespace App\Tests\D07Bundle;

use \PHPUnit\Framework\TestCase;
use App\D07Bundle\Service\Ex03Service;

class Ex03ServiceTest extends TestCase
{
    public function testUppercaseWords()
    {
        $service = new Ex03Service();
        $this->assertEquals('Hello World', $service->uppercaseWords('hello world'));
        $this->assertEquals('Foo Bar', $service->uppercaseWords('foO bAr'));
        $this->assertEquals('', $service->uppercaseWords(''));
    }

    public function testCountNumbers()
    {
        $service = new Ex03Service();
        $this->assertEquals(6, $service->countNumbers('abc123def456'));
        $this->assertEquals(0, $service->countNumbers('no numbers here'));
        $this->assertEquals(1, $service->countNumbers('one1'));
    }
}