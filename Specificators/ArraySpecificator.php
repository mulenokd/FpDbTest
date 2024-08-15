<?php

namespace FpDbTest\Specificators;

use FpDbTest\Specificators\Exceptions\InvalidArgumentTypeException;

class ArraySpecificator extends AbstractSpecificator
{
    /**
     * @throws InvalidArgumentTypeException
     */
    public function validate(): void
    {
        if (!is_array($this->binding)) {
            throw new InvalidArgumentTypeException('Get ' . gettype($this->binding) . ' type, but accepted only array');   
        }

        foreach ($this->binding as $value) {
            (new DefaultSpecificator($value))->validate($value);
        }
    }

    public function toQueryString(?callable $closure = null): string
    {
        $this->validate();
        
        $binding = $this->binding;

        foreach ($binding as $key => $value) {
            $binding[$key] = (new DefaultSpecificator($value))->toQueryString($closure);
        }

        return implode(', ', $this->makeNotAssocArray($binding));
    }

    private function makeNotAssocArray(array $array): array
    {
        if ($array === array_values($array)) {
            return $array;
        }

        foreach ($array as $key => $value) {
            $array[$key] = sprintf("`%s` = %s", $key, $value);
        }

        return $array;
    }
}