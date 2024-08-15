<?php

namespace FpDbTest\Specificators;

use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;

class IdSpecificator extends AbstractSpecificator
{
    /**
     * @throws InvalidArgumentTypeException
     */
    public function validate(): void
    {
        if (is_int($this->binding) || is_string($this->binding)) {
            return;
        }

        if (is_array($this->binding)) {
            foreach ($this->binding as $value) {
                if (!is_string($value) && !is_int($value)) {
                    throw new InvalidArgumentTypeException(
                        'Get ' . gettype($value) . ' type, but accepted only numeric string, int, array of ints, array of numeric string'
                    );
                }
            }
            return;
        }

        throw new InvalidArgumentTypeException(
            'Get ' . gettype($this->binding) . ' type, but accepted only numeric string, int, array of ints, array of numeric string'
        );
    }

    public function toQueryString(?callable $closure = null): string
    {
        $this->validate();

        if (is_string($this->binding) && $closure) {
            return sprintf("`%s`", $closure($this->binding));
        }

        if (is_array($this->binding)) {
            return implode(', ', array_map(fn($v) => is_string($v) ? sprintf("`%s`", $closure ? $closure($v) : $v) : $v, $this->binding));
        }
        
        return (string) $this->binding;
    }
}