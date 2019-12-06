<?php

namespace rvkulikov\amo\module\services\init;

use DateTime;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\models\App_OauthState;
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
        $this->security = Instance::ensure($this->security, Security::class);
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

        $user = App_User::findOne(['username' => $cfg->username]);
        $user = $user ?? App_User::findOne(['email' => $cfg->userEmail]);
        $user = $res->user = $user ?? new App_User(['username' => $cfg->username, 'email' => $cfg->userEmail]);

        $user->status = App_User::STATUS_ACTIVE;
        $user->password = $res->password = $this->security->generateRandomString(64);
        $user->auth_key = $res->authKey = $this->security->generateRandomString(64);

        if (!$user->save()) {
            throw new InvalidModelException($user);
        }

        $role = $res->role = new Role(['name' => 'role:amo:admin']);
        $this->authManager->revokeAll($user->id);
        $this->authManager->assign($role, $user->id);

        $integration = Integration::findOne(['id' => $cfg->integrationId]);
        $integration = $res->integration = $integration ?? new Integration(['id' => $cfg->integrationId]);

        $integration->secret_key = $cfg->integrationSecretKey;
        $integration->redirect_uri = $cfg->integrationRedirectUri;

        if (!$integration->save()) {
            throw new InvalidModelException($integration);
        }

        $state = $res->state = new App_OauthState([
            'user_id' => $user->id,
            'integration_id' => $integration->id,
            'token' => $this->security->generateRandomString(64),
            'expires_at' => (new DateTime('now'))->modify('+20 minutes')->format(DATE_RFC3339)
        ]);
        if (!$state->save()) {
            throw new InvalidModelException($state);
        }

        $url = "https://www.amocrm.ru/oauth/";
        $query = http_build_query([
            'client_id' => $integration->id,
            'state' => $state->token,
        ]);

        $res->oauthGrantUrl = "{$url}?{$query}";

        return $res;
    }
}