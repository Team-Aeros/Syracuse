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
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Auth extends Model {

    /**
     * Creates a new instance of the Auth class
     */
    public function __construct() {
        $this->loadSettings();

        if (!empty($_POST))
            $this->login();

        if (rand(0, 50) == 1)
            Token::cleanUp();
    }

    /**
     * Checks the entered credentials with the database
     * @return int The user id if the user exists, else 0
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

    /**
     * Checks if the user is logged in
     * @return bool|Token Token value if logged in, false if not
     */
    public function isLoggedIn() : bool {
        if (empty($_SESSION['logged_in']))
            return false;

        return (new Token($_SESSION['logged_in']))->verify();
    }

    /**
     * Logs the user out by setting the session value 'logged_in' on null
     * @return void
     */
    public function logOut() {
        $_SESSION['logged_in'] = null;
        header('Location: ' . self::$config->get('url'));
    }

    /**
     * Logs the user in by calling checkCred to validate credentials and if correct setting the session value logged_in token value
     * @return void
     */
    private function login() {
        if ($userId = $this->checkCred() > 0) {
            $token = Token::generate($userId);

            $_SESSION['logged_in'] = $token->getValue();
        }
    }
}



