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

class Auth extends Model {

    private $errors;


    public function __construct() {
        if (!empty($_POST))
            $this->login();

        $this->errors = array();
    }

    /*
     * Retrieves the username from the database
     * returns String name
     */
    private function getName(){
        $names = Database::interact('retrieve', 'accounts')
            ->fields('name')
            ->getAll();
        $array = $names[0];
        return $array['name'];
    }

    /*
     * Retrieves the password that belongs to the username
     * Parameter $name = username
     * returns String password
     */
    private function getPass($name) {
        $db_name = $name;
        $pass = Database::interact('retrieve', 'accounts')
            ->fields('pass')
            ->where(['name', $db_name])
            ->getAll();
        $array = $pass[0];
        return $array['pass'];
    }

    /*
     * Retrieves the salt from the database
     * returns Array salt
     */
    private function getSalt() {
        $salt = Database::interact('retrieve', 'setting')
            ->fields('identifier', 'val')
            ->where(['identifier', 'salt'])
            ->getAll();
        $Array = $salt[0];
        $saltArray = $Array['val'];
        $saltArray2 = ['salt' => $saltArray];
        return $saltArray2;

    }

    /*
     * Hashes the entered password using bcrypt
     * Parameter $salt = Array salt
     * Parameter $pass = String password
     * returns String hashed password
     */
    private function hashPass($salt, $pass) {
        $hashPass = password_hash($pass, PASSWORD_BCRYPT, $salt);
        return $hashPass;
    }

    private function errorMsg() {
        if(empty($this->errors)) {
            $this->errors[0] = "Username or password was incorrect";
        } else {
            echo count($this->errors);
        }
    }


    public function getErrors() {
        return $this->errors;
    }

    /*
     * Checks the entered credentials with the database
     * Returns true if correct, false if not
     */

    private function checkCred() {
        if (!empty($_POST['username'] && !empty($_POST['password']))) {
            $db_name = $this->getName();
            if ($_POST['username'] === $db_name) {
                $db_pass = $this->getPass($db_name);
                if ($this->hashPass($this->getSalt(), $_POST['password']) === $db_pass) {
                    return true;
                }
            }
        }
        return false;
    }


    public function isLoggedIn() : bool {
        return $_SESSION['logged_in'] ?? false;
    }


    private function login() {
        if($this->checkCred()) {
            $_SESSION['logged_in'] = true;
        } else {
            $this->errorMsg();}
    }
}



