<?php

namespace FpDbTest;

use FpDbTest\Exceptions\ArgumentNotFoundException;
use FpDbTest\Specificators\Enums\SpecificatorDictionary;
use mysqli;
use FpDbTest\Specificators\Factories\SpecificatorFactory;

class Database implements DatabaseInterface
{
    private mysqli $mysqli;
    private SpecificatorFactory $specificatorFactory;

    public function __construct(mysqli $mysqli)
    {
        $this->mysqli = $mysqli;
        $this->specificatorFactory = new SpecificatorFactory();
    }

    public function buildQuery(string $query, array $bindings = []): string
    {
        return $this->build($query, $bindings);
    }

    public function skip()
    {
        return 'skip';
    }

    private function build(string $query, array $bindings = []): string
    {
        $queryAsCharArray = str_split($query);
        $logicBlockFirstCharPosition = 0;
        $currentArg = 0;
        $queryBindings = [];
        $needToSkipLogicBlockChars = [];

        foreach($queryAsCharArray as $charKey => $char){
            if ($char === '{') {
                $logicBlockFirstCharPosition = $charKey;
            }
            if ($char === '}') {
                $logicBlockFirstCharPosition = 0;
            }
            if ($queryAsCharArray[$charKey - 1] === '?') {
                if (!array_key_exists($currentArg, $bindings)) {
                    throw new ArgumentNotFoundException();
                }

                if ($logicBlockFirstCharPosition && $bindings[$currentArg] === $this->skip()) {
                    $needToSkipLogicBlockChars[] = [
                        'firstCharPosition' => $logicBlockFirstCharPosition,
                        'lastCharPosition' => strpos($query, '}', $logicBlockFirstCharPosition),
                    ];
                    $currentArg++;
                    continue;
                }

                $queryBindings[] = $this->specificatorFactory
                    ->create(trim('?' . $char), $bindings[$currentArg])
                    ->toQueryString(fn($binding) => $this->mysqli->real_escape_string($binding));
                $currentArg++;
            }
        }

        foreach ($needToSkipLogicBlockChars as $logicBlock) {
            $length = $logicBlock['lastCharPosition'] - $logicBlock['firstCharPosition'] + 1;
            $query = substr_replace($query, '', $logicBlock['firstCharPosition'], $length);
        }

        $query = str_replace(['{', '}'], '', $query);
        $query = str_replace(SpecificatorDictionary::LIST, '%s', $query);

        return vsprintf($query, $queryBindings);
    }
}
