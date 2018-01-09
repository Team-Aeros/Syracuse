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

final class Registry {

    private static $_objects = [];

    private function __construct() {}

    public static function store(string $identifier, $object) {
        if (!empty(self::$_objects[$identifier]))
            earlyExit('A registry error occurred', sprintf('Cannot store object in registry, as identifier \'%s\' is already in use', $identifier));

        self::$_objects[$identifier] = $object;
        return self::$_objects[$identifier];
    }

    public static function retrieve(string $identifier) {
        if (!isset(self::$_objects[$identifier]))
            earlyExit('A registry error occurred', sprintf('Cannot retrieve object with identifier \'%s\' from registry. It does not exist', $identifier));

        return self::$_objects[$identifier];
    }
}