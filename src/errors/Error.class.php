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

namespace Syracuse\src\errors;

/**
 * Class Error
 * @package Syracuse\src\errors
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Error {

    /**
     * The error message
     */
    protected $message;

    /**
     * Detailed information about the error message
     */
    protected $detailedError;

    /**
     * Whether or not the error is fatal
     */
    protected $isFatal;

    /**
     * Error constructor.
     * @param string $message, the message of the error
     * @param string $detailedError, the details of the error
     * @param bool $fatal, whether the error is fatal or not
     */
    public function __construct(string $message, string $detailedError, bool $fatal = true) {
        $this->message = $message;
        $this->detailedError = $detailedError;
        $this->isFatal = $fatal;
    }

    /**
     * Checks if an template can be loaded
     * @return bool Whether or not the template can be loaded
     */
    protected function canLoadTemplate() : bool {
        return defined('LOADED_TEMPLATE_AND_LANG');
    }

    /**
     * Triggers an error when called.
     * @return void
     */
    public function trigger() : void {
        ob_clean();

        if ($this->canLoadTemplate())
            echo 'This is an error with an error template.';
        else
            echo '<strong>An error occurred:</strong> ' . $this->message;

        if (SYRACUSE_DEBUG)
            echo ' Since debug mode is enabled, we can tell you stuff. This error was returned:<hr />' . $this->detailedError;

        if ($this->isFatal)
            die;
    }
}