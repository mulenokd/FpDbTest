<?php

namespace FpDbTest\Tests;

use FpDbTest\Specificators\ArraySpecificator;
use FpDbTest\Specificators\DefaultSpecificator;
use FpDbTest\Specificators\Enums\SpecificatorDictionary;
use FpDbTest\Specificators\Exceptions\SpecificatorNotFoundException;
use FpDbTest\Specificators\Factories\SpecificatorFactory;
use FpDbTest\Specificators\FloatSpecificator;
use FpDbTest\Specificators\IdSpecificator;
use FpDbTest\Specificators\Interfaces\SpecificatorInterface;
use FpDbTest\Specificators\IntSpecificator;

class SpecificatorFactoryTest
{
    private SpecificatorFactory $specificatorFactory;

    public function __construct()
    {
        $this->specificatorFactory = new SpecificatorFactory();
    }

    public function testShouldReturnIntSpecificator(): bool
    {
        return $this->getSpecificator(SpecificatorDictionary::INT) instanceof IntSpecificator;
    }

    public function testShouldReturnFloatSpecificator(): bool
    {
        return $this->getSpecificator(SpecificatorDictionary::FLOAT) instanceof FloatSpecificator;
    }

    public function testShouldReturnArraySpecificator(): bool
    {
        return $this->getSpecificator(SpecificatorDictionary::ARRAY) instanceof ArraySpecificator;
    }

    public function testShouldReturnIdSpecificator(): bool
    {
        return $this->getSpecificator(SpecificatorDictionary::ID) instanceof IdSpecificator;
    }
    
    public function testShouldReturnDefaultSpecificator(): bool
    {
        return $this->getSpecificator(SpecificatorDictionary::DEFAULT) instanceof DefaultSpecificator;
    }

    public function testShouldThrowExceptionForWrongSpecificator(): bool
    {
        try {
            $specificator = $this->specificatorFactory->create('wrong', []);
        } catch (SpecificatorNotFoundException $e) {
            return true;
        }

        return false;
    }

    private function getSpecificator(string $specificator): SpecificatorInterface
    {
        return $this->specificatorFactory->create($specificator, []);
    }
}