<?php

namespace FpDbTest\Tests;

use Exception;
use FpDbTest\Specificators\ArraySpecificator;
use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;
use FpDbTest\Tests\Traits\WithMysqli;

class ArraySpecificatorTest
{
    use WithMysqli;

    public function testShouldThrowExceptionWithStringBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException('name');
    }

    public function testShouldThrowExceptionWithIntBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException(1);
    }

    public function testShouldThrowExceptionWithFloatBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException(10.56);
    }

    public function testShouldThrowExceptionWithNullBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException(null);
    }

    public function testShouldThrowExceptionWithClassBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException(new class{});
    }

    public function testShouldThrowExceptionWithObjectBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException((object)[]);
    }

    public function testShouldReturnEscapedQueryBindingsWithNonAssocArray(): bool
    {
        return $this->buildQueryString([0, null, 2.56, 'email'], '0, NULL, 2.56, \'email\'');
    }

    public function testShouldReturnEscapedQueryBindingsWithAssocArray(): bool
    {
        return $this->buildQueryString(
            ['name' => 'Jack', 'email' => 'jack@gmail.com'],
            '`name` = \'Jack\', `email` = \'jack@gmail.com\''
        );
    }

    public function testShouldReturnEscapedQueryBindingsWithNullableAssocArray(): bool
    {
        return $this->buildQueryString(
            ['name' => 'Jack', 'email' => null],
            '`name` = \'Jack\', `email` = NULL'
        );
    }

    private function buildQueryString($bindings, string $expectedString): bool
    {
        $specificator = new ArraySpecificator($bindings);
        
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