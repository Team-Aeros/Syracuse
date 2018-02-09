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

/**
 * Class Login
 * @package Syracuse\src\modules\models
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Login extends Model {

    /**
     * Fills the errors array if there is an error with logging in
     * @param array $errors, the errors array
     * @return void
     */
    public function login(array &$errors) : void {
        // I'm an error
        if(!empty($_POST)) {
            $errors['cred'] = '******E-mail or password incorrect******';
        }
    }
}