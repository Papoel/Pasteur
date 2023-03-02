<?php

declare(strict_types=1);

namespace App\Tests\Unit;

use PHPUnit\Framework\TestCase;

class StartingTest extends TestCase
{
    public function testTwoStringsAreIdentical(): void
    {
        $string1 = 'starting test by @Papoel';
        $string2 = 'starting test by @Papoel';

        $this->assertSame($string1, $string2);
        $this->assertTrue(condition: $string1 === $string2);
    }
}
