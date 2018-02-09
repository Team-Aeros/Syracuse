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
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class GUI extends Model {

    /**
     * Path to the template directory
     */
    private $_templateDir;

    /**
     * The current page title
     */
    private $_pageTitle;

    /**
     * @return string _pageTitle
     */
    public function getPageTitle() : string {
        return $this->_pageTitle ?? _translate('dashboard');
    }

    /**
     * @return string _templateDir
     */
    public function getTemplateDir() : string {
        return $this->_templateDir;
    }

    /**
     * Sets the page title
     * @param string $pageTitle The page title
     * @return void
     */
    public function setPageTitle(string $pageTitle) : void {
        $this->_pageTitle = $pageTitle;
    }

    /**
     * Function for setting the Template dir.
     * @param string $templateDir The path to the template directory
     * @return void
     */
    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }
}