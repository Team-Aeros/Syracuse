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

namespace Syracuse\src\main\controllers;

use Syracuse\Config;
use Syracuse\src\auth\models\Auth;
use Syracuse\src\core\controllers\Route;
use Syracuse\src\core\models\Registry;
use Syracuse\src\core\models\ReturnCode;
use Syracuse\src\database\{Connection, Database};

/**
 * Class Syracuse
 * @package Syracuse\src\main\controllers
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Syracuse {

    /**
     * This property contains the GUI object
     */
    private $_gui;

    /**
     * This property contains the Config object
     */
    private $_config;

    /**
     * This property contains route information
     */
    private $_route;

    /**
     * Creates a new instance of Syracuse
     */
    public function __construct() {
        $this->_config = Registry::store('config', new Config());
        $this->_route = Registry::store('route', new Route());
        $this->setErrorHandler();
    }

    /**
     * Starts the software by initializing things like the database connection, language loading, etc.
     * @return void
     */
    public function start() : void {
        Database::setConnection(new Connection(
            $this->_config->get('database_server'),
            $this->_config->get('database_username'),
            $this->_config->get('database_password'),
            $this->_config->get('database_name'),
            $this->_config->get('database_prefix')
        ));

        // This removes database information from the configuration object
        $this->_config->wipeSensitiveData();

        $settings = Database::interact('retrieve', 'setting')
            ->fields('identifier', 'val')
            ->getAll();

        $this->_config->import($settings);
        $auth = Registry::store('auth', new Auth());

        $page = $this->_route->getRouteInfo()['module_name'] ?? 'main';
        if (!$auth->isLoggedIn() && $page != 'login' && $page != 'help') {
            header('Location: ' . $this->_config->get('url') . '/index.php/login');
            die;
        }

        Registry::store('lang', new Language());

        // Yep, we're perfectly aware this is not a great solution, but hey, it works
        $this->_gui = Registry::store('gui', new GUI());

        // NOTE: Template and language loading should be done BEFORE this constant is set
        define('LOADED_TEMPLATE_AND_LANG', true);

        $moduleLoader = new ModuleLoader($this->_route);
        $moduleLoader->load();
        $module = $moduleLoader->getModule();

        $returnCode = $module->execute();

        if (empty($this->_route->getRouteInfo()['parameters']['ajax_request']))
            $this->_gui->displayTemplate('header');

        if ($returnCode !== ReturnCode::SUCCESS) {
            switch ($returnCode) {
                case ReturnCode::PERMISSION_DENIED:
                    $message = 'permission_denied';
                    logError('permissions', _translate($message), __FILE__, __LINE__);
                    break;
                case ReturnCode::RECORD_NOT_FOUND:
                    $message = 'record_not_found';
                    logError('record_not_found', _translate($message), __FILE__, __LINE__);
                    break;
                case ReturnCode::CANNOT_ENDANGER_CONNECTION:
                case ReturnCode::DATABASE_ERROR:
                case ReturnCode::INVALID_QUERY_TYPE:
                    $message = 'database_error';
                    logError('database', _translate($message), __FILE__, __LINE__);
                    break;
                case ReturnCode::NOT_IMPLEMENTED:
                    // Not adding these to the error log, since we'd already know something isn't implemented yet
                    $message = 'not_implemented';
                    break;
                case ReturnCode::GENERAL_ERROR:
                default:
                    logError('general', _translate($message), __FILE__, __LINE__);
                    $message = 'general_error';
            }

            $this->_gui->displayTemplate('error', ['message' => $message, 'title' => $message . '_title']);
        }

        else
            $module->display();

        if (empty($this->_route->getRouteInfo()['parameters']['ajax_request']))
            $this->_gui->displayTemplate('footer');
    }

    /**
     * Sets the PHP error handler
     * @return void
     */
    private function setErrorHandler() : void {
        set_error_handler(function(int $errorNumber, string $errorMessage, ?string $errorFile = '', ?int $errorLine = 0, ?array $errorContext = []) {
            if (SYRACUSE_DEBUG) {
                logError('php', $errorMessage, $errorFile, $errorLine);
                echo '<strong>AN ERROR OCCURRED:</strong> ', $errorMessage, ' in ', $errorFile, ' on line ', $errorLine, '<br />';
            }
        }, E_ALL);
    }
}