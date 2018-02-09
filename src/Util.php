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
 * @return string The language string
 */
function _translate(string $identifier, ?string ...$parameters) : string {
    $lang = Registry::retrieve('lang');
    $langString = $lang->read($identifier);

    return !empty($parameters) ? sprintf($langString, $parameters) : $langString;
}

/**
 * Adds an entry to the log file.
 * @param string $type The error type. Possible values:
 *      - database
 *      - php
 *      - general
 *      - template
 *      - authentication
 *      - language
 *      - permissions
 *      - record_not_found
 *      - core
 *      - module
 * @param string $message The error message
 * @param string $filename The filename where the error was thrown
 * @param int $line The line where the error was thrown
 * @param bool $printAnyway Whether or not to print the error to the screen even if a log entry could not be created
 * @return void
 */
function logError(string $type, string $message, string $filename, int $line, bool $printAnyway = true) : void {
    if (!SYRACUSE_DEBUG || !ENABLE_ERROR_LOGGING)
        return;

    $file = fopen(__DIR__ . '/../log.txt', 'a');

    if (!$file) {
        if ($printAnyway)
            printf('Could not add error message to log. Requested by %s on line %u', $message, $filename, $line);

        return;
    }

    switch ($type) {
        case 'database':
            $errorType = 'database error';
            break;
        case 'php':
            $errorType = 'PHP error';
            break;
        case 'template':
            $errorType = 'template error';
            break;
        case 'authentication':
            $errorType = 'authentication error';
            break;
        case 'language':
            $errorType = 'language';
            break;
        case 'permissions':
            $errorType = 'access denied';
            break;
        case 'record_not_found':
            $errorType = 'unknown record';
            break;
        case 'module':
            $errorType = 'module error';
            break;
        case 'core':
            $errorType = 'core error';
            break;
        case 'general':
        default:
            $errorType = 'general';
    }

    $textToWrite = sprintf('[%u] (%s): %s in %s on line %u', time(), $errorType, $message, $filename, $line) . "\n";

    fwrite($file, $textToWrite);
    fclose($file);
}