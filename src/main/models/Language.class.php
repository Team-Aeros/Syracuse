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

/**
 * Class Language
 * @package Syracuse\src\main\models
 */
class Language extends Model {

    private $_id;
    private $_name;
    private $_native;
    private $_code;

    /**
     * Language constructor.
     * @param int $id
     */
    public function __construct(int $id) {
        $this->loadSettings();
        $this->load($id);
    }

    /**
     * @param int $id
     */
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

    /**
     * Function for returning the id.
     * @return int
     */
    public function getId() : int {
        return $this->_id;
    }

    /**
     * Function for returning the name.
     * @return string
     */
    public function getName() : string {
        return $this->_name;
    }

    /**
     * Function for returning the native language.
     * @return string
     */
    public function getNative() : string {
        return $this->_native;
    }

    /**
     * Function for returning the language code.
     * @return string
     */
    public function getCode() : string {
        return $this->_code;
    }

    /**
     * @param string $code
     * @return null|Language
     */
    public static function loadByCode(string $code) : ?self {
        $language = Database::interact('retrieve', 'language')
            ->where('code', $code)
            ->max(1)
            ->getSingle();

        if (empty($language['id'])) {
            logError('language', sprintf('Could not load language by code \'%s\'', $code), __FILE__, __LINE__);
            return null;
        }

        return new self($language['id']);
    }
}