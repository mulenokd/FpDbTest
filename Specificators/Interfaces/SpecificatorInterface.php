<?php

namespace FpDbTest\Specificators\Interfaces;

interface SpecificatorInterface
{
    public function __construct($argument);

    public function validate(): void;

    public function toQueryString(?callable $closure = null): string;
}