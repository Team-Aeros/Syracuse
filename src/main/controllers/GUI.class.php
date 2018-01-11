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

use Dwoo;
use Syracuse\src\headers\ControllerHeader;
use Syracuse\src\main\models\GUI as Model;

class GUI extends ControllerHeader {

    private $_model;

    private $_core;

    public function __construct() {
        $this->_core = new Dwoo\Core();

        $this->_model = new Model();
    }

    public function displayMainTemplate() : void {
        echo $this->_core->get($this->_model->getTemplateDir() . '/main.tpl', $this->_model->getData());
    }

    public function setPageTitle(string $pageTitle) : void {
        $this->_model->setPageTitle($pageTitle);
    }

    public function setTemplateDir(string $templateDir) : void {
        $this->_model->setTemplateDir($templateDir);
    }
}