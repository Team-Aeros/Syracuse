<?php

/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Aeros Development
 * @copyright   2017-2018 Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

namespace Syracuse\src\headers;

interface Module {

    public function __construct(string $moduleName, array $parameters);
    public function execute() : int;
    public function display() : void;
}