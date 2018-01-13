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

use Syracuse\src\core\models\Registry;

function earlyExit(string $message, ?string $detailedError = null) : void {
    printf('<strong>An error occurred:</strong> %s %s', $message, !empty($detailedError) && SYRACUSE_DEBUG ? '<br />' . $detailedError : '');
    die;
}

function dumpAndDie(array $elements) : void {
    echo '<pre>';
    print_r($elements);
    echo '</pre>';
}

function _translate(string $identifier, ?string ...$parameters) : string {
    $lang = Registry::retrieve('lang');
    $langString = $lang->read($identifier);

    return !empty($parameters) ? sprintf($langString, $parameters) : $langString;
}