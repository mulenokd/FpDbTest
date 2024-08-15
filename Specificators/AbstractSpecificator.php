<?php

namespace FpDbTest\Specificators;

use FpDbTest\Specificators\Interfaces\SpecificatorInterface;

abstract class AbstractSpecificator implements SpecificatorInterface
{
    protected $binding;

    public function __construct($binding)
    {
        $this->binding = $binding;
    }
}