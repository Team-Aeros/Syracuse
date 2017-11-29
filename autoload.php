<?php

/**
 * Syracuse
 *
 * @version     1.0 Beta 1
 * @author      Team Aeros
 * @copyright   2017, Syracuse
 * @since       1.0 Beta 1
 *
 * @license     MIT
 */

spl_autoload_register(function ($class) {
    $parsed = explode('\\', $class);

    unset($parsed[0]);

    if (count($parsed) == 0)
        $path = __DIR__ . '/' . $class . '.class.php';
    else
        $path = __DIR__ . '/' . implode('/', $parsed) . '.class.php';

    if (!file_exists($path))
        return false;

    require_once $path;
    return true;
});