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

function earlyExit(string $message, ?string $detailedError = null) {
    printf('<strong>An error occurred:</strong> %s %s', $message, !empty($detailedError) && SYRACUSE_DEBUG ? '<br />' . $detailedError : '');
    die;
}