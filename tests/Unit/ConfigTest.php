<?php

declare(strict_types = 1);

namespace Test\Unit;

use App\Config;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(Config::class)]
class ConfigTest extends TestCase
{
    private $configData = [
        'host' => 'localhost',
        'port' => 3306,
    ];

    public function testOffsetExists()
    {
        $config = new Config($this->configData);
        $this->assertTrue(isset($config['host']));
        $this->assertFalse(isset($config['unknown']));
    }

    public function testOffsetGet()
    {
        $config = new Config($this->configData);
        $this->assertEquals('localhost', $config['host']);
        $this->assertNull($config['unknown']);
    }

    public function testOffsetSetThrows()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Cannot modify configuration in this way");
        $config = new Config($this->configData);
        $config['new_key'] = 'value';
    }

    public function testOffsetUnsetThrows()
    {
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage("Cannot unset configuration");
        $config = new Config($this->configData);
        unset($config['host']);
    }
}