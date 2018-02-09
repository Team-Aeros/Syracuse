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

namespace Syracuse\src\modules\models;

use Syracuse\src\headers\Model;

/**
 * The help model. This model is primary used for generating help page title and body.
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Help extends Model {

    /**
     * Returns the data needed in the template.
     * @return array The data
     */
    public function getData() : array {
        return [
            'title' => 'help',
            'body' => 'help_body',
        ];
    }
}