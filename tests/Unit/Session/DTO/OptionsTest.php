<?php

declare(strict_types = 1);

namespace Test\Unit\Session\DTO;

use PHPUnit\Framework\TestCase;
use App\Session\DTO\Options;
use App\Session\Enum\SameSite;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(Options::class)]
class OptionsTest extends TestCase
{
    public function testDefaultValues()
    {
        $options = new Options(
            name: 'session_name'
        );

        $this->assertSame('session_name', $options->name);
        $this->assertSame(60 * 30, $options->lifetime);
        $this->assertSame('/', $options->path);
        $this->assertTrue($options->secure);
        $this->assertTrue($options->httpOnly);
        $this->assertSame(SameSite::Lax, $options->sameSite);
        $this->assertNull($options->storagePath);
    }
}