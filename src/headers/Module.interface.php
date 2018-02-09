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

/**
 * Interface Module
 * @package Syracuse\src\headers
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
interface Module {

    /**
     * Module constructor.
     * @param string $moduleName
     * @param array $parameters
     */
    public function __construct(string $moduleName, array $parameters);

    /**
     * Starts execution of the module
     * @return int The return code
     */
    public function execute() : int;

    /**
     * Displays the template
     * @return void
     */
    public function display() : void;
}