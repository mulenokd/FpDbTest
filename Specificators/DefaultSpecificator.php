<?php

namespace FpDbTest\Specificators;

use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;

class DefaultSpecificator extends AbstractSpecificator
{
    /**
     * @throws InvalidArgumentTypeException
     */
    public function validate(): void
    {
        if (
            is_int($this->binding) || 
            is_float($this->binding) || 
            is_null($this->binding) || 
            is_string($this->binding) || 
            is_bool($this->binding)
        ) {
            return;
        }

        throw new InvalidArgumentTypeException(
            'Get ' . gettype($this->binding) . ', but accepted only string, int, float, bool, null.'
        );
    }

    public function toQueryString(?callable $closure = null): string
    {
        $this->validate();

        if (is_string($this->binding) && $closure) {
            return (string) '\'' . $closure($this->binding) . '\'';
        }
        
        if (is_null($this->binding)) {
            return 'NULL';
        }

        $binding = is_bool($this->binding) ? intval($this->binding) : $this->binding;

        return (string) $binding;
    }
}