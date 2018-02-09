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

namespace Syracuse\src\auth\models;

use Syracuse\src\database\Database;

/**
 * Class Token
 * @package Syracuse\src\auth\models
 * @since 1.0 Beta 1
 * @author Aeros Development
 */
class Token {

    /**
     * The token value
     */
    private $_value;

    /**
     * The user id
     */
    private $_userId;

    /**
     * The length of the token. If you change this, make sure you update the database as well
     */
    private const LENGTH = 40;

    /**
     * Token constructor.
     * Set the value and ID.
     * @param string $value The token value
     * @param int|null $userId The user id
     */
    public function __construct(string $value, int $userId = null) {
        $this->_value = $value;
        $this->_userId = $userId ?? 0;
    }

    /**
     * Verifying ID.
     * @return bool Whether or not the token was valid
     */
    public function verify() : bool {
        if (strlen($this->_value) != self::LENGTH)
            return false;

        $result = Database::interact('retrieve', 'token')
            ->fields('value', 'created_at', 'length', 'user_id')
            ->where(['value', ':value'])
            ->placeholders(['value' => $this->_value])
            ->getSingle();

        if (empty($result))
            return false;

        $this->_userId = $result['user_id'];

        if (time() > (int) $result['created_at'] + $result['length'] && $result['length'] != 0)
            return false;

        return true;
    }

    /**
     * Generate token for later use.
     * @param int $userId What user is this token for?
     * @return Token The generated token
     */
    public static function generate(int $userId) : self {
        $token = '';

        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $charLength = strlen($chars);

        for ($i = 0; $i < self::LENGTH; $i++)
            $token .= $chars[rand(0, $charLength - 1)];

        Database::interact('insert', 'token')->placeholders([
                'value' => $token,
                'user_id' => $userId
            ])
            ->insert([
                'value' => ':value',
                'created_at' => time(),
                'user_id' => ':user_id',
                'length' => 60 * 60 * 24
        ]);

        return new self($token, $userId);
    }

    /**
     * Cleans up tokens that are no longer used
     * @return void
     */
    public static function cleanUp() : void {
        Database::interact('delete', 'token')
            ->whereCustom('created_at + length < :time')
            ->whereCustom('length != 0')
            ->placeholders(['time' => time()])
            ->delete();
    }

    /**
     * Function for returning value.
     * @return string The token value
     */
    public function getValue() : string {
        return $this->_value;
    }

    /**
     * Function for returning userId.
     * @return int The user id
     */
    public function getUserId() : int {
        return $this->_userId;
    }
}