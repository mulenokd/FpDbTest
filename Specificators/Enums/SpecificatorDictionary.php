<?php

namespace FpDbTest\Specificators\Enums;

class SpecificatorDictionary
{
    public const INT = '?d';
    public const FLOAT = '?f';
    public const ARRAY = '?a';
    public const ID = '?#';
    public const DEFAULT = '?';

    public const LIST = [
        self::INT,
        self::FLOAT,
        self::ARRAY,
        self::ID,
        self::DEFAULT
    ];
    
}