<?php
/**
 * Created by me.
 * User: Jelmer
 * Date: 15-Jan-18
 * Time: 09:56
 */

namespace Syracuse\src\auth\models;

use Syracuse\src\database\Database;
use Syracuse\src\headers\ControllerHeader;
use Syracuse\src\headers\ModelHeader;

class Auth extends ModelHeader {

    public function __construct() {
        if(session_status() == PHP_SESSION_NONE) {
            session_start();
            $_SESSION['logged_in'] = False;
        }
        $this->login();

    }

    private function getName(){
        $names = Database::interact('retrieve', 'accounts')
            ->fields('name')
            ->getAll();
        $array = $names[0];
        return $array['name'];
    }

    private function getPass($name) {
        $db_name = $name;
        $pass = Database::interact('retrieve', 'accounts')
            ->fields('pass')
            ->where(['name', $db_name])
            ->getAll();
        $array = $pass[0];
        return $array['pass'];
    }

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

    private function hashPass($salt, $pass) {
        $hashPass = password_hash($pass, PASSWORD_BCRYPT, $salt);
        return $hashPass;
    }

    private function errorMsg() {
        echo "Username or password was incorrect.";
    }


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
    private function login() {
        if($this->checkCred()) {
            $_SESSION['logged_in'] = True;
            header("Location: http://localhost/syracuse/");
        } else {
            $this->errorMsg();}
    }
}



