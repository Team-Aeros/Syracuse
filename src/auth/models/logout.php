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

if($_GET['btnLogOut']) {
    $_SESSION['logged_in'] = False;
}
echo "TEST";
header("Location: http://localhost/Syracuse/");

exit;