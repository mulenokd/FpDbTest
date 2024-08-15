<?php

namespace FpDbTest\Tests;

use Exception;
use FpDbTest\Specificators\DefaultSpecificator;
use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;
use FpDbTest\Tests\Traits\WithMysqli;

class DefaultSpecificatorTest
{
    use WithMysqli;

    public function testShouldThrowExceptionWithArrayBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException([]);
    }

    public function testShouldThrowExceptionWithClassBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException(new class{});
    }

    public function testShouldThrowExceptionWithObjectBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException((object)[]);
    }

    public function testShouldReturnQueryBindingsWithInt(): bool
    {
        return $this->buildQueryString(1, '1');
    }

    public function testShouldReturnQueryBindingsWithFloat(): bool
    {
        return $this->buildQueryString(1.56, '1.56');
    }

    public function testShouldReturnQueryBindingsWithFalseBoolean(): bool
    {
        return $this->buildQueryString(false, '0');
    }

    public function testShouldReturnQueryBindingsWithTrueBoolean(): bool
    {
        return $this->buildQueryString(true, '1');
    }

    public function testShouldReturnQueryBindingsWithNull(): bool
    {
        return $this->buildQueryString(null, 'NULL');
    }

    public function testShouldReturnEscapedQueryBindingsWithString(): bool
    {
        return $this->buildQueryString('Test String', '\'Test String\'');
    }

    private function buildQueryString($bindings, string $expectedString): bool
    {
        $specificator = new DefaultSpecificator($bindings);
        
        $result = $specificator->toQueryString(fn($binding) => $this->getMysqli()->real_escape_string($binding));

        if ($result !== $expectedString) {
            throw new Exception('Result: ' . $result . ', but expected: ' . $expectedString);
        }
        
        return true;
    }

    private function isCatchedInvalidArgumentTypeException($bindings): bool
    {
        try {
            $this->buildQueryString($bindings, '');
        } catch (InvalidArgumentTypeException $e) {
            return true;
        }

        return false;
    }
}