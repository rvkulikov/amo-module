<?php /** @noinspection PhpUndefinedVariableInspection */
return [
    'container' => [
        'definitions' => [
            'yii\test\InitDbFixture'                       => [
                'class' => 'yii\test\InitDbFixture',
                'db'    => $params['rvkulikov.amo.db.name'],
            ],
            'rvkulikov\amo\module\commands\InitController' => [
                'class'         => 'rvkulikov\amo\module\commands\InitController',
                'redirectUri'   => $params['rvkulikov.amo.tests.account.redirect_uri'],
                'secretKey'     => $params['rvkulikov.amo.tests.account.secret_key'],
                'integrationId' => $params['rvkulikov.amo.tests.account.integration_id'],
            ],
        ],
    ],
];