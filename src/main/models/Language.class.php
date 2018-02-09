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
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Language extends Model {

    /**
     * The language id
     */
    private $_id;

    /**
     * The language name (in English)
     */
    private $_name;

    /**
     * The language name in the language itself)
     */
    private $_native;

    /**
     * The language code (e.g. en_US, es_ES, de_CH)
     */
    private $_code;

    /**
     * Language constructor.
     * @param int $id The language id
     */
    public function __construct(int $id) {
        $this->loadSettings();
        $this->load($id);
    }

    /**
     * Loads a language by ID
     * @param int $id The language id
     * @return void
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
     * @return int _id
     */
    public function getId() : int {
        return $this->_id;
    }

    /**
     * @return string _name
     */
    public function getName() : string {
        return $this->_name;
    }

    /**
     * @return string _native
     */
    public function getNative() : string {
        return $this->_native;
    }

    /**
     * @return string _code
     */
    public function getCode() : string {
        return $this->_code;
    }

    /**
     * Loads a language by code
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