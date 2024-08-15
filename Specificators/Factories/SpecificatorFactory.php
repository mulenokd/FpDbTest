<?php

namespace FpDbTest\Specificators\Factories;

use FpDbTest\Specificators\Enums\SpecificatorDictionary;
use FpDbTest\Specificators\Exceptions\SpecificatorNotFoundException;
use FpDbTest\Specificators\Interfaces\SpecificatorInterface;
use FpDbTest\Specificators\ArraySpecificator;
use FpDbTest\Specificators\DefaultSpecificator;
use FpDbTest\Specificators\FloatSpecificator;
use FpDbTest\Specificators\IdSpecificator;
use FpDbTest\Specificators\IntSpecificator;

class SpecificatorFactory
{
    /**
     * @throws SpecificatorNotFoundException
     */
    public function create(string $specificator, $binding): SpecificatorInterface
    {
        switch ($specificator) {
            case SpecificatorDictionary::INT:
                return new IntSpecificator($binding);
            case SpecificatorDictionary::FLOAT:
                return new FloatSpecificator($binding);
            case SpecificatorDictionary::ARRAY:
                return new ArraySpecificator($binding);
            case SpecificatorDictionary::ID:
                return new IdSpecificator($binding);
            case SpecificatorDictionary::DEFAULT:
                return new DefaultSpecificator($binding);
        }

        throw new SpecificatorNotFoundException();
    }
}
