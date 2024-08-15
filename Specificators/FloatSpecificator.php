<?php

namespace FpDbTest\Specificators;

use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;

class FloatSpecificator extends AbstractSpecificator
{
    /**
     * @throws InvalidArgumentTypeException
     */
    public function validate(): void
    {
        if (is_null($this->binding) || is_numeric($this->binding) || is_bool($this->binding)) {
            return;
        }

        throw new InvalidArgumentTypeException(
            'Get ' . gettype($this->binding) . ' type, but accepted only boolean, numeric string, int, float, null.'
        );
    }

    public function toQueryString(?callable $closure = null): string
    {
        $this->validate();

        if (is_null($this->binding)) {
            return 'NULL';
        }
        
        return (string) floatval($this->binding);
    }
}