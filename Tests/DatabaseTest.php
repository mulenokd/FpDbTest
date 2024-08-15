<?php

namespace FpDbTest\Tests;

use Exception;
use FpDbTest\Database;
use FpDbTest\DatabaseInterface;
use FpDbTest\Exceptions\ArgumentNotFoundException;
use FpDbTest\Tests\Traits\WithMysqli;

class DatabaseTest
{
    use WithMysqli;

    private DatabaseInterface $db;

    public function __construct()
    {
        $this->db = new Database($this->getMysqli());
    }

    public function testShouldReturnQueryWithoutBindings(): bool
    {
        return $this->executeQuery(
            'SELECT name FROM users WHERE user_id = 1', 
            [],
            'SELECT name FROM users WHERE user_id = 1'
        );
    }

    public function testShouldReturnQueryWithDefaultSpecification(): bool
    {
        return $this->executeQuery(
            'SELECT * FROM users WHERE name = ? AND block = 0',
            ['Jack'],
            'SELECT * FROM users WHERE name = \'Jack\' AND block = 0'
        );
    }

    public function testShouldReturnQueryWithIdAndIntSpecifications(): bool
    {
        return $this->executeQuery(
            'SELECT ?# FROM users WHERE user_id = ?d AND block = ?d',
            [['name', 'email'], 2, true],
            'SELECT `name`, `email` FROM users WHERE user_id = 2 AND block = 1'
        );
    }

    public function testShouldReturnQueryWithArraySpecification(): bool
    {
        return $this->executeQuery(
            'UPDATE users SET ?a WHERE user_id = -1',
            [['name' => 'Jack', 'email' => null]],
            'UPDATE users SET `name` = \'Jack\', `email` = NULL WHERE user_id = -1'
        );
    }

    public function testShouldReturnQueryWithLogicBlockThatShouldBeRemoved(): bool
    {
        return $this->executeQuery(
            'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
            ['user_id', [1, 2, 3], null],
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = NULL'
        );
    }

    public function testShouldReturnQueryWithLogicBlockThatShouldBeSkipped(): bool
    {
        return $this->executeQuery(
            'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
            ['user_id', [1, 2, 3], $this->db->skip()],
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3)'
        );
    }

    public function testShouldReturnQueryWithLogicBlockThatShouldNotBeRemoved(): bool
    {
        return $this->executeQuery(
            'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}',
            ['user_id', [1, 2, 3], true],
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1'
        );
    }

    public function testShouldReturnQueryWithTwoLogicBlocks(): bool
    {
        return $this->executeQuery(
            'SELECT name FROM users WHERE ?# IN (?a){ AND block = ?d}{ AND block = ?d}',
            ['user_id', [1, 2, 3], $this->db->skip(), true],
            'SELECT name FROM users WHERE `user_id` IN (1, 2, 3) AND block = 1'
        );
    }

    public function testShouldThrowExceptionWithWrongCountOfBindings(): bool
    {
        try {
            $this->db->buildQuery('SELECT name FROM users WHERE ?# IN (?a)', ['user_id']);
        } catch (ArgumentNotFoundException $e) {
            return true;
        }

        return false;
    }

    private function executeQuery(string $query, array $args = [], string $expectedString): string
    {
        $result = $this->db->buildQuery($query, $args);

        if ($result !== $expectedString) {
            throw new Exception('Result: ' . $result . ', but expected: ' . $expectedString);
        }
        
        return true;
    }
}
