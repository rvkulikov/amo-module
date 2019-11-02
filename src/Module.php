<?php
namespace rvkulikov\amo\module;

use rvkulikov\amo\module\commands\MigrateController;
use Yii;
use yii\base\Application;
use yii\base\BootstrapInterface;
use yii\base\InvalidConfigException;
use yii\console\Application as CliApplication;
use yii\console\controllers\FixtureController;
use yii\helpers\ArrayHelper;
use yii\rbac\ManagerInterface;
use yii\rest\UrlRule;
use yii\web\Application as WebApplication;
use yii\web\GroupUrlRule;

/**
 * @property-read ManagerInterface $authManager
 */
class Module extends \yii\base\Module implements BootstrapInterface
{
    /**
     * {@inheritDoc}
     */
    public function __construct($id, $parent = null, $config = [])
    {
        /** @noinspection PhpUnusedLocalVariableInspection */
        $isWeb  = Yii::$app instanceof WebApplication;
        $isCli  = Yii::$app instanceof CliApplication;
        $isTest = YII_ENV_TEST;

        parent::__construct($id, $parent, ArrayHelper::merge([
            'controllerNamespace' => $isCli
                ? __NAMESPACE__ . '\commands'
                : __NAMESPACE__ . '\controllers',
            'controllerMap'       => array_filter($isCli || $isTest ? [
                'migrate' => [
                    'class'         => MigrateController::class,
                    'migrationPath' => __DIR__ . '/migrations',
                    'db'            => Yii::$app->params['rvkulikov.amo.db.name'],
                ],
                'fixture' => [
                    'class'     => FixtureController::class,
                    'namespace' => 'rvkulikov\amo\module\tests\codeception\fixtures',
                ],
            ] : []),
        ], $config));
    }

    /**
     * @param Application $app
     *
     * @throws InvalidConfigException
     */
    public function bootstrap($app)
    {
        if ($app instanceof WebApplication) {
            $app->urlManager->addRules([
                Yii::createObject([
                    'class'      => GroupUrlRule::class,
                    'ruleConfig' => [
                        'class'     => UrlRule::class,
                        'pluralize' => false,
                    ],
                    'rules'      => [
                        ['controller' => "{$this->uniqueId}/accounts"],
                        ['controller' => "{$this->uniqueId}/groups"],
                        ['controller' => "{$this->uniqueId}/users"],
                    ],
                ]),
            ], false);
        }
    }
}