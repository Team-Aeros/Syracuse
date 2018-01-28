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

namespace Syracuse\src\core\models;

final class Registry {

    private static $_objects = [];

    private function __construct() {}

    public static function store(string $identifier, $object) {
        if (!empty(self::$_objects[$identifier])) {
            $error = sprintf('Cannot store object in registry, as identifier \'%s\' is already in use', $identifier);
            logError('core', $error, __FILE__, __LINE__);
            earlyExit('A registry error occurred', $error);
        }

        self::$_objects[$identifier] = $object;
        return self::$_objects[$identifier];
    }

    public static function retrieve(string $identifier) {
        if (!isset(self::$_objects[$identifier])) {
            $error = sprintf('Cannot retrieve object with identifier \'%s\' from registry. It does not exist', $identifier);
            logError('core', $error, __FILE__, __LINE__);
            earlyExit('A registry error occurred', $error);
        }

        return self::$_objects[$identifier];
    }
}