<?php

abstract class Test {

    protected $methods;

    public function __construct() {
        $this->methods = [

        ];
    }

    protected function assert(bool $condition, string $file, string $line) : void {
        echo '<strong>Assertion ', ($condition ? 'successful' : 'failed'), '</strong> in ', $file, ' on line ', $line, '<br />';

        if (!$condition)
            die;
    }

    public function run() : void {
        foreach ($this->methods as $method) {
            $this->assert($method[0], $method[1], $method[2]);
        }
    }
}