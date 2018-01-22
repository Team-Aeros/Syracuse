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

/**
 * Prints an error message and prevents the script from executing any further. If debug mode is enabled,
 * a detailed error message will be printed as well (if there is one, of course).
 * @param string $message A user-friendly error message (note: should NOT contain sensitive information)
 * @param string $detailedError A message containing more detailed information about the error
 * @return void
 */
function earlyExit(string $message, ?string $detailedError = null) : void {
    printf('<strong>An error occurred:</strong> %s %s', $message, !empty($detailedError) && SYRACUSE_DEBUG ? '<br />' . $detailedError : '');
    die;
}

/**
 * Inspired by Laravel's function, this function takes an array, prints it recursively
 * and prevents further execution of the script.
 * @param array $elements The array that should be printed
 * @return void
 */
function dumpAndDie(array $elements) : void {
    echo '<pre>';
    print_r($elements);
    die('</pre>');
}

/**
 * Loads a string from the language file and returns the result.
 * @param string $identifier The string identifier
 * @param \string[] Replacements. Used for replacing things like %s and %u with these replacements (optional)
 * @return string
 */
function _translate(string $identifier, ?string ...$parameters) : string {
    $lang = Registry::retrieve('lang');
    $langString = $lang->read($identifier);

    return !empty($parameters) ? sprintf($langString, $parameters) : $langString;
}