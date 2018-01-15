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

spl_autoload_register(function ($class) {
    $parsed = explode('\\', $class);

    unset($parsed[0]);

    if (count($parsed) == 0)
        $path = __DIR__ . '/' . $class;
    else if (count($parsed) == 1)
        $path = __DIR__ . '/public/' . $parsed[1];
    else
        $path = __DIR__ . '/' . implode('/', $parsed);

    if (file_exists($path . '.class.php'))
        $fileLocation = $path . '.class.php';
    else if (file_exists($path . '.interface.php'))
        $fileLocation = $path . '.interface.php';
    else if (file_exists($path . '.php'))
        $fileLocation = $path . '.php';
    else
        return false;

    require_once $fileLocation;
    return true;
});