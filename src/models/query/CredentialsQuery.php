<?php

namespace rvkulikov\amo\module\models\query;

use rvkulikov\amo\module\models\Credentials;
use yii\db\ActiveQuery;

/**
 * @see \rvkulikov\amo\module\models\Credentials
 */
class CredentialsQuery extends ActiveQuery
{
    /**
     * @return CredentialsQuery
     */
    public function active()
    {
        return $this->andWhere(['is', '[[deleted_at]]', null]);
    }

    /**
     * {@inheritdoc}
     * @return Credentials[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return Credentials|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
