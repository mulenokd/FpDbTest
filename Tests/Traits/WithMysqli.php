<?php

namespace FpDbTest\Tests\Traits;

use Exception;
use mysqli;

trait WithMysqli
{
    public function getMysqli(): mysqli
    {
        $mysqli = @new mysqli($_ENV['DB_HOST'], $_ENV['DB_USER'], $_ENV['DB_PASSWORD'], $_ENV['DB_NAME'], $_ENV['DB_PORT']);
        if ($mysqli->connect_errno) {
            throw new Exception($mysqli->connect_error);
        }

        return $mysqli;
    }
}