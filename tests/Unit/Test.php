<?php

declare(strict_types = 1);

namespace Test\Unit;

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testThatTestsWork()
    {
        $var1 = 2;
        $var2 = 1;

        $sum = $var1 + $var2;
        $sub = $var1 - $var2;

        $this->assertSame(2 + 1, $sum);
        $this->assertSame(2 - 1, $sub);

        // that should fail
        $this->assertSame(2 - 0, $sub);
    }
}