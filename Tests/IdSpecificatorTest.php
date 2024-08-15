<?php

namespace FpDbTest\Tests;

use Exception;
use FpDbTest\Specificators\IdSpecificator;
use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;
use FpDbTest\Tests\Traits\WithMysqli;

class IdSpecificatorTest
{
    use WithMysqli;

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

    public function testShouldThrowExceptionWithArrayOfFloatsBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException([2.56, 10.22]);
    }

    public function testShouldThrowExceptionWithArrayOfBooleansBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException([true, false]);
    }

    public function testShouldThrowExceptionWithArrayOfNullsBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException([null, null]);
    }

    public function testShouldThrowExceptionWithArrayOfArraysBindings(): bool
    {
        return $this->isCatchedInvalidArgumentTypeException([[], []]);
    }

    public function testShouldReturnEscapedQueryBindingsWithArrayOfInts(): bool
    {
        return $this->buildQueryString([0, 1, 2], '0, 1, 2');
    }

    public function testShouldReturnEscapedQueryBindingsWithArrayOfStrings(): bool
    {
        return $this->buildQueryString(['name', 'email'], '`name`, `email`');
    }

    public function testShouldReturnEscapedQueryBindingsWithAssocArray(): bool
    {
        return $this->buildQueryString(
            ['name' => 'Jack', 'email' => 'jack@gmail.com'],
            '`Jack`, `jack@gmail.com`'
        );
    }

    private function buildQueryString($bindings, string $expectedString): bool
    {
        $specificator = new IdSpecificator($bindings);
        
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