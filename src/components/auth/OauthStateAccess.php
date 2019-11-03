<?php

namespace rvkulikov\amo\module\components\auth;

use yii\filters\auth\QueryParamAuth;

/**
 *
 */
class OauthStateAccess extends QueryParamAuth
{
    public $tokenParam = 'state';
}