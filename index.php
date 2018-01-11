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

const MIN_PHP_VERSION = '7.1.0';
const SOFTWARE_VERSION = '1.0 Beta 1';
const SYRACUSE_DEBUG = true;

ob_start();
session_start();

if (version_compare(phpversion(), MIN_PHP_VERSION, '<'))
    die(sprintf('You are running an unsupported PHP version. Syracuse requires PHP %s. Aborting...', MIN_PHP_VERSION));

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/autoload.php';
require_once __DIR__ . '/src/Util.php';
require_once __DIR__ . '/public/index.php';