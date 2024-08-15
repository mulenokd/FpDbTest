<?php

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