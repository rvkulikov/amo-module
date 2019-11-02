<?php
namespace rvkulikov\amo\module\services\init;

use rvkulikov\amo\module\models\App_User;
use rvkulikov\amo\module\models\Integration;
use yii\base\Component;
use yii\base\Exception;
use yii\base\InvalidConfigException;
use yii\base\Security;
use yii\di\Instance;
use yii\rbac\ManagerInterface;
use yii\rbac\Role;

/**
 *
 */
class ModuleInitializer_Impl extends Component implements ModuleInitializer_Interface
{
    /** @var ManagerInterface */
    public $authManager;
    /** @var Security */
    public $security;

    /**
     * {@inheritDoc}
     * @throws InvalidConfigException
     */
    public function __construct($config = [])
    {
        parent::__construct($config);

        $this->authManager = Instance::ensure($this->authManager, ManagerInterface::class);
        $this->security    = Instance::ensure($this->security, Security::class);
    }

    /**
     * @param ModuleInitializer_Cfg $cfg
     *
     * @return ModuleInitializer_Res
     * @throws Exception
     * @throws \Exception
     */
    public function initialize(ModuleInitializer_Cfg $cfg)
    {
        $res = new ModuleInitializer_Res();

        $user = $res->user = App_User::findOne(['username' => $cfg->username]) ?? new App_User(['username' => $cfg->username]);

        $user->email    = $cfg->userEmail;
        $user->status   = $cfg->userStatus;
        $user->password = $res->password = $this->security->generateRandomString(64);
        $user->auth_key = $res->authKey = $this->security->generateRandomString(64);

        $user->save();

        $role = $res->role = new Role(['name' => 'role:admin']);
        $this->authManager->revokeAll($user->id);
        $this->authManager->assign($role, $user->id);

        $integration = $res->integration = Integration::findOne(['id' => $cfg->integrationId]) ?? new Integration(['id' => $cfg->integrationId]);

        $integration->secret_key   = $cfg->integrationSecretKey;
        $integration->redirect_uri = $cfg->integrationRedirectUri;

        $integration->save();

        $res->oauthGrantUrl = "https://www.amocrm.ru/oauth/?client_id={$integration->id}&integration_id={$integration->id}&access-token={$user->auth_key}";

        return $res;
    }
}