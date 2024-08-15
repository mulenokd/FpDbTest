<?php

namespace FpDbTest;

use Exception;
use FpDbTest\Tests\ArraySpecificatorTest;
use FpDbTest\Tests\DatabaseTest;
use FpDbTest\Tests\DefaultSpecificatorTest;
use FpDbTest\Tests\FloatSpecificatorTest;
use FpDbTest\Tests\IdSpecificatorTest;
use FpDbTest\Tests\IntSpecificatorTest;
use FpDbTest\Tests\SpecificatorFactoryTest;
use Throwable;

class TestExtended
{
    public function __construct()
    {
        try {
            $this->includeProject();

            $this->executeTest(new SpecificatorFactoryTest());
            $this->executeTest(new ArraySpecificatorTest());
            $this->executeTest(new DefaultSpecificatorTest());
            $this->executeTest(new IntSpecificatorTest());
            $this->executeTest(new FloatSpecificatorTest());
            $this->executeTest(new IdSpecificatorTest());
            $this->executeTest(new DatabaseTest());
        } catch (\Throwable $th) {
            echo $th->getMessage();
        }
    }

    public function includeProject() {
        $env = file_get_contents(__DIR__ . "/.env");
        $lines = explode("\n",$env);

        foreach($lines as $line){
            preg_match("/([^#]+)\=(.*)/", $line, $matches);
            if(isset($matches[2])){
                putenv(trim($line));
            }
        } 

        spl_autoload_register(function ($class) {
            $a = array_slice(explode('\\', $class), 1);
            if (!$a) {
                throw new Exception();
            }
            $filename = implode('/', [__DIR__, ...$a]) . '.php';
            require_once $filename;
        });
    }

    public function executeTest($class)
    {
        $methods = get_class_methods($class);

        foreach($methods as $method) {
            if (str_starts_with($method, 'test')) {
                try {
                    $result = $class->{$method}();
                    $this->colorLog(get_class($class) . '::' . $method . '()', $result ? 's' : 'e');
                } catch (Throwable $e) {
                    $this->colorLog(get_class($class) . '::' . $method . '()' . ' - Exception: ' . $e->getMessage(), 'e');
                }
            }
        }
    }

    public function colorLog(string $str, string $type = 'i') 
    {
        switch ($type) {
            case 'e': //error
                echo "\033[31m$str \033[0m\n";
            break;
            case 's': //success
                echo "\033[32m$str \033[0m\n";
            break;
            case 'w': //warning
                echo "\033[33m$str \033[0m\n";
            break;  
            case 'i': //info
                echo "\033[36m$str \033[0m\n";
            break;      
            default:
                echo $str;
            break;
        }
    }
}

require './autoload.php';

$test = new TestExtended();
