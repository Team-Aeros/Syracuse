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

use Syracuse\src\headers\Controller;
use Syracuse\src\main\models\GUI as Model;
use Twig_Environment;
use Twig_Function;
use Twig_Loader_Filesystem;

class GUI extends Controller {

    private $_model;
    private $_twig;
    private $_loader;

    private $_defaultData;

    public function __construct() {
        $this->_model = new Model();

        $this->_defaultData = [];

        $this->loadSettings();
        $this->setTemplateDir(self::$config->get('path') . '/public/views/' . self::$config->get('theme') . '/templates');

        $this->_loader = new Twig_Loader_Filesystem($this->_model->getTemplateDir());
        $this->_twig = new Twig_Environment($this->_loader, [
            'cache' => self::$config->get('path') . '/cache',
            'debug' => SYRACUSE_DEBUG
        ]);

        $this->setDefaultData();

        $this->_twig->addFunction(new Twig_Function('_translate', function(string $identifier, string ...$params) {
            return _translate($identifier, ...$params);
        }));

        $this->_twig->addFunction(new Twig_Function('count', function(array $arr) {
            return count($arr);
        }));
    }

    public function displayTemplate(string $template, array $data = []) : void {
        echo $this->_twig->load($template . '.tpl')->render($this->_defaultData + $data);
    }

    private function setDefaultData() : void {
        $this->_defaultData = [
            'template_dir' => $this->_model->getTemplateDir(),
            'image_url' => self::$config->get('url') . '/public/views/' . self::$config->get('theme') . '/images',
            'stylesheet_url' => self::$config->get('url') . '/public/views/' . self::$config->get('theme') . '/css',
            'base_url' => self::$config->get('url'),
            'script_url' => self::$config->get('url') . '/public/scripts',
            'node_url' => self::$config->get('url') . '/node_modules',
            'page_title' => $this->_model->getPageTitle()
        ];
    }

    public function setPageTitle(string $pageTitle) : void {
        $this->_model->setPageTitle($pageTitle);
    }

    public function setTemplateDir(string $templateDir) : void {
        $this->_model->setTemplateDir($templateDir);
    }
}