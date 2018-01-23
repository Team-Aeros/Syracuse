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

use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\headers\Model;

class Login extends Model {

    public function login(array &$errors) : void {
        // I'm an error
        if(!empty($_POST)) {
            $errors['cred'] = '******E-mail or password incorrect******';
        }
    }
}