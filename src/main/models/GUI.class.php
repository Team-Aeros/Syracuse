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

namespace Syracuse\src\main\models;

use Syracuse\src\headers\Model;

class GUI extends Model {

    private $_templateDir;
    private $_pageTitle;

    public function getPageTitle() : string {
        return $this->_pageTitle ?? _translate('dashboard');
    }

    public function getTemplateDir() : string {
        return $this->_templateDir;
    }

    public function setPageTitle(string $pageTitle) : void {
        $this->_pageTitle = $pageTitle;
    }

    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }
}