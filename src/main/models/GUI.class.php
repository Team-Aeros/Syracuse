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

/**
 * Class GUI
 * @package Syracuse\src\main\models
 */
class GUI extends Model {

    private $_templateDir;
    private $_pageTitle;

    /**
     * Function for returning the page title.
     * @return string
     */
    public function getPageTitle() : string {
        return $this->_pageTitle ?? _translate('dashboard');
    }

    /**
     * Function for returning the template dir.
     * @return string
     */
    public function getTemplateDir() : string {
        return $this->_templateDir;
    }

    /**
     * Function for setting the page title.
     * @param string $pageTitle
     */
    public function setPageTitle(string $pageTitle) : void {
        $this->_pageTitle = $pageTitle;
    }

    /**
     * Function for setting the Template dir.
     * @param string $templateDir
     */
    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }
}