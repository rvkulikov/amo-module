<?php

namespace rvkulikov\amo\module\models;

use DateTime;
use rvkulikov\amo\module\components\auth\OauthStateAccess;
use Yii;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Security;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\di\Instance;
use yii\web\IdentityInterface;

/**
 * @property int $id                   [bigint]
 * @property string $username             [varchar(255)]
 * @property string $email                [varchar(255)]
 * @property string $auth_key             [varchar(64)]
 * @property string $password_hash        [varchar(255)]
 * @property string $password_reset_token [varchar(255)]
 * @property string $verification_token   [varchar(255)]
 * @property int $status               [smallint]
 * @property int $created_at           [timestamp(0)]
 * @property int $updated_at           [timestamp(0)]
 *
 * @property-write string $password
 * @property-read  string $authKey
 * @property-read  App_OauthState $oauthStates
 */
class App_User extends ActiveRecord implements IdentityInterface
{
    const STATUS_DELETED = 0;
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    /**
     * @var Security
     */
    public $sec = 'security';

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->params['rvkulikov.amo.db.name']);
    }

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'app__user';
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne([
            'id'     => $id,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        if ($type === OauthStateAccess::class) {
            $query = static::find();
            $query->alias('u');
            $query->joinWith(['oauthStates s'], false);
            $query->where([
                'and',
                ['>', 's.expires_at', (new DateTime())->format('Y-m-d H:i:s')],
                ['s.token' => $token],
                ['u.status' => self::STATUS_ACTIVE]
            ]);

            YII_DEBUG && $sql = $query->createCommand()->rawSql;

            return $query->one();
        } else {
            return static::findOne([
                'auth_key' => $token,
                'status'   => self::STATUS_ACTIVE,
            ]);
        }
    }

    /**
     * Finds user by username
     *
     * @param string $username
     *
     * @return App_User|null
     */
    public static function findByUsername($username)
    {
        return static::findOne([
            'username' => $username,
            'status'   => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     *
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status'               => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     *
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int)substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire']; // todo params.php
        return $timestamp + $expire >= time();
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     *
     * @return static|null
     */
    public static function findByVerificationToken($token)
    {
        return static::findOne([
            'verification_token' => $token,
            'status'             => self::STATUS_INACTIVE,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => [
                'class'              => TimestampBehavior::class,
                'createdAtAttribute' => 'created_at',
                'updatedAtAttribute' => 'updated_at',
                'value'              => new Expression('NOW()'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['status', 'default', 'value' => self::STATUS_INACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE, self::STATUS_DELETED]],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * @return ActiveQuery
     */
    public function getOauthStates()
    {
        return $this->hasMany(App_OauthState::class, ['user_id' => 'id'])->inverseOf('user');
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     *
     * @return bool if password provided is valid for current user
     * @throws InvalidConfigException
     */
    public function validatePassword($password)
    {
        $this->sec = Instance::ensure($this->sec, Security::class);
        return $this->sec->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     *
     * @throws Exception
     */
    public function setPassword($password)
    {
        $this->sec = Instance::ensure($this->sec, Security::class);

        $this->password_hash = $this->sec->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     *
     * @throws Exception
     */
    public function generateAuthKey()
    {
        $this->sec = Instance::ensure($this->sec, Security::class);

        $this->auth_key = $this->sec->generateRandomString();
    }

    /**
     * Generates new password reset token
     *
     * @throws Exception
     */
    public function generatePasswordResetToken()
    {
        $this->sec = Instance::ensure($this->sec, Security::class);

        $this->password_reset_token = $this->sec->generateRandomString() . '_' . time();
    }

    /**
     * @throws Exception
     */
    public function generateEmailVerificationToken()
    {
        $this->sec = Instance::ensure($this->sec, Security::class);

        $this->verification_token = $this->sec->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }
}