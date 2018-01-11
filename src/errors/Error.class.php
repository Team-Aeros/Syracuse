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

class Error {

    protected $message;
    protected $detailedError;
    protected $isFatal;

    public function __construct(string $message, string $detailedError, bool $fatal = true) {
        $this->message = $message;
        $this->detailedError = $detailedError;
        $this->isFatal = $fatal;
    }

    protected function canLoadTemplate() : bool {
        return defined('LOADED_TEMPLATE_AND_LANG');
    }

    public function trigger() : void {
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