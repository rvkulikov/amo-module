<?php
namespace rvkulikov\amo\module\tests\models;

use yii\base\BaseObject;
use yii\web\IdentityInterface;

/**
 * @property mixed $authKey
 */
class User extends BaseObject implements IdentityInterface
{
    const ID_TEST = 1;

    public $id;
    public $auth_key;
    public $username;

    /**
     * {@inheritDoc}
     */
    public static function findIdentity($id)
    {
        return new User([
            'id'       => self::ID_TEST,
            'username' => 'user',
            'auth_key' => 'token',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        return new User([
            'id'       => self::ID_TEST,
            'username' => 'user',
            'auth_key' => 'token',
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritDoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritDoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->auth_key === $authKey;
    }
}