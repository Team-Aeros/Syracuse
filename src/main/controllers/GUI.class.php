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

use Syracuse\src\core\models\Registry;
use Syracuse\src\headers\Controller;
use Syracuse\src\main\models\GUI as Model;

/**
 * This class is used for loading the graphical user interface and serves as an API to the
 * template engine.
 * @since   1.0 Beta 1
 * @author  Aeros Development
 */
class GUI extends Controller {

    /**
     * @var GUI $_model The GUI model stores paths and other important data
     */
    private $_model;

    /**
     * @var TemplateManager $_templateManager An instance of the TemplateManager class
     */
    private $_templateManager;

    /**
     * @var array $_defaultData This associative array contains data that should always be available to templates
     */
    private $_defaultData;

    /**
     * Creates a new instance of the GUI class and sets the paths.
     * @return void
     */
    public function __construct() {
        $this->_model = new Model();

        $this->_defaultData = [];

        $this->loadSettings();
        $this->loadAuthenticationSystem();
        $this->setTemplateDir(self::$config->get('path') . '/public/views/' . self::$config->get('theme') . '/templates');

        $this->_templateManager = new TemplateManager();
        $this->_templateManager->setTemplateDir($this->_model->getTemplateDir());
        $this->_templateManager->setCacheDir(self::$config->get('path') . '/cache');

        $this->setDefaultData();
    }

    /**
     * Prints the requested template to the screen
     * @param string $template The template name (without the extension)
     * @param array $data Additional variables that should be available to the template
     * @return void
     */
    public function displayTemplate(string $template, array $data = []) : void {
        $this->_templateManager->getTemplate($template, $this->_defaultData + $data);
    }

    /**
     * Sets default data. These variables are available to all templates and are loaded automatically.
     * @return void
     */
    private function setDefaultData() : void {
        $this->_defaultData = [
            'template_dir' => $this->_model->getTemplateDir(),
            'image_url' => self::$config->get('url') . '/public/views/' . self::$config->get('theme') . '/images',
            'stylesheet_url' => self::$config->get('url') . '/public/views/' . self::$config->get('theme') . '/css',
            'base_url' => self::$config->get('url'),
            'script_url' => self::$config->get('url') . '/public/scripts',
            'node_url' => self::$config->get('url') . '/node_modules',
            'page_title' => $this->_model->getPageTitle(),
            'is_logged_in' => self::$auth->isLoggedIn(),
            'on_login_page' => Registry::retrieve('route')->getRouteInfo()['module_name'] == 'login'
        ];
    }

    /**
     * Sets the page title
     * @param string $pageTitle The desired page title
     * @param void
     */
    public function setPageTitle(string $pageTitle) : void {
        $this->_model->setPageTitle($pageTitle);
    }

    /**
     * Sets the template dir
     * @param string $templateDir The template dir
     * @param void
     */
    public function setTemplateDir(string $templateDir) : void {
        $this->_model->setTemplateDir($templateDir);
    }
}