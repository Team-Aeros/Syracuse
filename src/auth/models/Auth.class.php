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

namespace Syracuse\src\auth\models;

use Syracuse\src\database\Database;
use Syracuse\src\headers\Model;

/**
 * If, in a hypothetical future scenario, support for multiple users is
 * added, this class will still work, but a check needs to be
 * added to see which user is currently logged in.
 */
class Auth extends Model {

    public function __construct() {
        $this->loadSettings();

        if (!empty($_POST))
            $this->login();

        if (rand(0, 50) == 1)
            Token::cleanUp();
    }

    /*
     * Checks the entered credentials with the database
     * Returns true if correct, false if not
     */
    private function checkCred() : int {
        if (empty($_POST['email']) || empty($_POST['password']))
            return 0;

        $user = Database::interact('retrieve', 'accounts')
            ->fields('id', 'name', 'pass')
            ->where(
                ['name', ':email']
            )
            ->placeholders([
                'email' => $_POST['email']
            ])
            ->getSingle();

        if (empty($user) || !password_verify($_POST['password'], $user['pass']))
            return 0;

        return $user['id'];
    }


    public function isLoggedIn() : bool {
        if (empty($_SESSION['logged_in']))
            return false;

        return (new Token($_SESSION['logged_in']))->verify();
    }

    public function logOut() {
        $_SESSION['logged_in'] = null;
        header('Location: ' . self::$config->get('url'));
    }


    private function login() {
        if ($userId = $this->checkCred() > 0) {
            $token = Token::generate($userId);

            $_SESSION['logged_in'] = $token->getValue();
        }
    }
}



