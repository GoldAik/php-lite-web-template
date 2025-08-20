<?php

declare(strict_types = 1);

namespace Test\Unit;

class TestHelper
{
    public static function getProperty(object $object, string $propertyName)
    {
        $reflection = new \ReflectionClass($object);
        $property = $reflection->getProperty($propertyName);
        $property->setAccessible(true);
        return $property->getValue($object);
    }
}