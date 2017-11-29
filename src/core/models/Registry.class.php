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

namespace Syracuse\src\core\models;

use Exception;

final class Registry {

    private static $_objects = [];

    public static function store(string $identifier, $object) {
        if (!empty(self::$_objects[$identifier]))
            throw new Exception(sprintf('Cannot store object in registry, as identifier \'%s\' is already in use', $identifier));

        self::$_objects[$identifier] = $object;
        return self::$_objects[$identifier];
    }

    public static function retrieve(string $identifier) {
        if (!isset(self::$_objects[$identifier]))
            throw new Exception(sprintf('Cannot retrieve object with identifier \'%s\' from registry. It does not exist', $identifier));

        return self::$_objects[$identifier];
    }
}