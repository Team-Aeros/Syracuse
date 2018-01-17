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

use Syracuse\src\database\Database;
use Syracuse\src\headers\Model;

class Language extends Model {

    private $_id;
    private $_name;
    private $_native;
    private $_code;

    public function __construct(int $id) {
        $this->loadSettings();
        $this->load($id);
    }

    private function load(int $id) : void {
        $languages = Database::interact('retrieve', 'language')
            ->fields('id', 'name', 'native', 'code')
            ->where(['id', $id])
            ->getAll();

        foreach ($languages as $language) {
            $this->_id = $language['id'];
            $this->_name = $language['name'];
            $this->_native = $language['native'];
            $this->_code = $language['code'];
        }
    }

    public function getId() : int {
        return $this->_id;
    }

    public function getName() : string {
        return $this->_name;
    }

    public function getNative() : string {
        return $this->_native;
    }

    public function getCode() : string {
        return $this->_code;
    }

    public static function loadByCode(string $code) : ?self {
        $result = Database::interact('retrieve', 'language')
            ->where('code', $code)
            ->getAll();

        foreach ($result as $record)
            return new self($record['id']);

        return null;
    }
}