<?php
namespace rvkulikov\amo\module\models;

use DateTime;
use Exception;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * @property int          $id                [bigint]
 * @property int          $account_id        [bigint]
 * @property int          $account_subdomain [varchar(255)]
 * @property string       $integration_id    [varchar(255)]
 * @property string       $secret_key        [varchar(255)]
 * @property string       $redirect_uri      [varchar(255)]
 * @property string       $token_type        [varchar(255)]
 * @property string       $expires_in        [integer]
 * @property int          $expires_at        [timestamp]
 * @property string       $access_token
 * @property string       $refresh_token     [varchar(255)]
 *
 * @property-write int    $expiresIn
 * @property-read Account $account
 */
class Credentials extends ActiveRecord
{
    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public static function getDb()
    {
        return Yii::$app->get(Yii::$app->params['rvkulikov.amo.db.name']);
    }

    /**
     * {@inheritDoc}
     */
    public static function tableName()
    {
        return '{{%amo__credentials}}';
    }

    /**
     * {@inheritDoc}
     */
    public function rules()
    {
        return [
            ['account_id', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'id'],
            ['account_id', 'required'],

            ['account_subdomain', 'exist', 'targetClass' => Account::class, 'targetAttribute' => 'subdomain'],
            ['account_subdomain', 'required'],

            ['integration_id', 'string'],
            ['integration_id', 'required'],

            ['secret_key', 'string'],
            ['secret_key', 'required'],

            ['redirect_uri', 'url'],
            ['redirect_uri', 'required'],

            ['token_type', 'string'],
            ['token_type', 'required'],

            ['expires_in', 'integer', 'min' => 0],

            ['expires_at', 'datetime', 'format' => 'php:Y-m-d H:i:s'],

            ['access_token', 'string'],

            ['refresh_token', 'string'],
            ['refresh_token', 'required'],
        ];
    }

    /**
     * @param int $value seconds
     *
     * @return $this
     * @throws Exception
     */
    public function setExpiresIn(int $value)
    {
        $this->expires_in = $value;
        $this->expires_at = (new DateTime('now'))->modify("+{$value} seconds")->format('Y-m-d H:i:s');

        return $this;
    }

    /**
     * @return ActiveQuery
     */
    public function getAccount()
    {
        return $this->hasOne(Account::class, ['id' => 'account_id'])->inverseOf('credentials');
    }
}