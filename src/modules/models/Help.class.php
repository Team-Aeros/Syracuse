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

class Help extends Model {

    public function getData() : array {
        return [
            'title' => 'help',
            'body' => 'help_body',
        ];
    }
}