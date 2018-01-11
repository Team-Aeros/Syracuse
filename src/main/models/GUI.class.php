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

use Dwoo;
use Syracuse\src\headers\ModelHeader;

class GUI extends ModelHeader {

    private $_templateDir;
    private $_pageTitle;
    private $_data;

    public function __construct() {
        $this->_data = new Dwoo\Data();
    }

    public function setData(array $data) : void {
        foreach ($data as $key => $value)
            $this->_data->assign($key, $value);
    }

    public function getPageTitle() : string {
        return $this->_pageTitle;
    }

    public function getTemplateDir() : string {
        return $this->_templateDir;
    }

    public function getData() : Dwoo\Data {
        return $this->_data;
    }

    public function setPageTitle(string $pageTitle) : void {
        $this->_pageTitle = $pageTitle;
    }

    public function setTemplateDir(string $templateDir) : void {
        $this->_templateDir = $templateDir;
    }
}