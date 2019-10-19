<?php
namespace rvkulikov\amo\module\tests\fixtures;

use Yii;
use yii\helpers\ArrayHelper;

/**
 *
 */
class ActiveFixture extends \yii\test\ActiveFixture
{
    public $dataDirectory = __DIR__ . '/../codeception/_data/models';

    /**
     * PollsActiveFixture constructor.
     *
     * @param array $config
     */
    public function __construct($config = [])
    {
        parent::__construct(ArrayHelper::merge([
            'db' => Yii::$app->params['rvkulikov.amo.db.name'],
        ], $config));
    }
}