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

if(!empty($_POST['logout'] == 'Log out')) {
    $_SESSION['logged_in'] = False;
}