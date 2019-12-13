<?php

namespace rvkulikov\amo\module\services\user\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\Status;
use rvkulikov\amo\module\models\User;
use rvkulikov\amo\module\models\UserAccount;
use rvkulikov\amo\module\models\UserAccountRights;
use rvkulikov\amo\module\models\UserGroup;
use rvkulikov\amo\module\models\UserStatusRights;
use rvkulikov\amo\module\services\group\sync\GroupSyncer_Interface;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;
use Throwable;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\helpers\ArrayHelper;

/**
 *
 */
class UserSyncer_Impl extends Component implements UserSyncer_Interface
{
    /** @var UserSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;
    /** @var GroupSyncer_Interface */
    private $groupSyncer;
    /** @var SafeDeleter_Interface */
    private $safeDeleter;

    /**
     * {@inheritDoc}
     */
    public function __construct(GroupSyncer_Interface $groupSyncer, SafeDeleter_Interface $safeDeleter, $config = [])
    {
        parent::__construct($config);
        $this->groupSyncer = $groupSyncer;
        $this->safeDeleter = $safeDeleter;
    }

    /**
     * @param array|UserSyncer_Cfg $cfg
     *
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Throwable
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, UserSyncer_Cfg::class);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        Account::getDb()->transaction(function () {
            $groups = $this->cfg->groups;
            $users  = array_map(function ($user) {
                $user['user_id']    = $user['id'];
                $user['account_id'] = $this->cfg->accountId;
                return $user;
            }, $this->cfg->users);

            $this->syncGroups($groups);
            $this->syncUsers($users);
            $this->syncUserAccounts($users);
            $this->syncUserAccountRights($users);
            $this->syncUserGroups($users);
            $this->syncUserStatusRights($users);
        });
    }

    /**
     * @param mixed[] $users
     */
    private function syncUsers($users)
    {
        array_walk($users, function ($user) {
            $model = null;
            $model = $model ?? User::findOne(['id' => $user['id']]);
            $model = $model ?? new User(['id' => $user['id']]);

            $model->load([
                'name'         => $user['name'],
                'last_name'    => $user['last_name'],
                'login'        => $user['login'],
                'language'     => $user['language'],
                'phone_number' => $user['phone_number'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        // no safeDelete check since user cannot be deleted, only unlinked
    }

    /**
     * @param $users
     *
     * @throws InvalidConfigException
     * @throws Exception
     * @throws InvalidModelException
     */
    private function syncUserAccounts($users)
    {
        array_walk($users, function ($user) {
            $model = null;
            $model = $model ?? UserAccount::findOne(['user_id' => $user['id'], 'account_id' => $this->account->id]);
            $model = $model ?? new UserAccount(['user_id' => $user['id'], 'account_id' => $this->account->id]);

            $model->load([
                'is_active' => $user['is_active'],
                'is_free'   => $user['is_free'],
                'is_admin'  => $user['is_admin'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $this->safeDeleter->delete([
            'definition' => UserAccount::class,
            'strategy'   => 'hard',
            'rows'       => $users,
            'where'      => ['account_id' => $this->account->id],
        ]);
    }

    /**
     * @param $users
     *
     * @throws InvalidConfigException
     * @throws Exception
     * @throws InvalidModelException
     */
    private function syncUserGroups($users)
    {
        array_walk($users, function ($user) {
            $model = null;
            $model = $model ?? UserGroup::findOne(['user_id' => $user['id'], 'group_id' => $user['group_id']]);
            $model = $model ?? new UserGroup(['user_id' => $user['id'], 'group_id' => $user['group_id']]);

            $model->load([
                'account_id' => $user['account_id'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $this->safeDeleter->delete([
            'definition' => UserGroup::class,
            'strategy'   => 'hard',
            'rows'       => $users,
            'where'      => ['account_id' => $this->account->id],
        ]);
    }

    /**
     * @param $users
     *
     * @throws Exception
     * @throws InvalidConfigException
     * @throws InvalidModelException
     */
    private function syncUserAccountRights($users)
    {
        $rights = array_map(function ($user) {
            $rights               = $user['rights'];
            $rights['user_id']    = $user['id'];
            $rights['account_id'] = $this->cfg->accountId;
            return $rights;
        }, $this->cfg->users);


        array_walk($rights, function ($item) {
            $pk    = UserAccountRights::primaryKey();
            $model = null;
            $model = $model ?? UserAccountRights::findOne(array_intersect_key($item, array_flip($pk)));
            $model = $model ?? new UserAccountRights(array_intersect_key($item, array_flip($pk)));

            $model->load([
                'incoming_leads' => $item['incoming_leads'],
                'catalogs'       => $item['catalogs'],
                'lead_add'       => $item['lead_add'],
                'lead_view'      => $item['lead_view'],
                'lead_edit'      => $item['lead_edit'],
                'lead_export'    => $item['lead_export'],
                'contact_add'    => $item['contact_add'],
                'contact_view'   => $item['contact_view'],
                'contact_edit'   => $item['contact_edit'],
                'contact_delete' => $item['contact_delete'],
                'contact_export' => $item['contact_export'],
                'company_add'    => $item['company_add'],
                'company_view'   => $item['company_view'],
                'company_edit'   => $item['company_edit'],
                'company_delete' => $item['company_delete'],
                'company_export' => $item['company_export'],
                'task_edit'      => $item['task_edit'],
                'task_delete'    => $item['task_delete'],
            ], '');

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $this->safeDeleter->delete([
            'definition' => UserAccountRights::class,
            'strategy'   => 'hard',
            'rows'       => $users,
            'where'      => ['account_id' => $this->account->id],
        ]);
    }

    /**
     * @param $users
     *
     * @throws Exception
     * @throws InvalidConfigException
     */
    private function syncUserStatusRights($users)
    {
        $rows = array_merge(...array_map(function ($user) {
            $pipelines = ArrayHelper::getValue($user, ['rights', 'by_status', 'leads'], []);

            $res = [];
            foreach ($pipelines as $pipelineId => $statuses) {
                foreach ($statuses as $statusId => $rights) {
                    $res[] = [
                        'user_id'     => $user['id'],
                        'account_id'  => $this->account->id,
                        'pipeline_id' => $pipelineId,
                        'status_id'   => $statusId,
                        'view'        => $rights['view'],
                        'edit'        => $rights['edit'],
                        'delete'      => $rights['delete'],
                        'export'      => $rights['export'],
                    ];
                }
            }

            return $res;
        }, $users));

        $index      = ArrayHelper::index($rows, 'status_id');
        $statusIds  = array_keys($index);
        $knownIds   = Status::find()->select(['id'])->where(['id' => $statusIds])->column();
        $unknownIds = array_diff($statusIds, $knownIds);
        $statuses   = array_intersect_key($index, array_flip($unknownIds));
        array_walk($statuses, function ($status) {
            $model = new Status([
                'account_id'  => $this->account->id,
                'pipeline_id' => $status['pipeline_id'],
                'id'          => $status['status_id'],
                'is_editable' => false,
            ]);

            if (!$model->save()) {
                throw new InvalidModelException($model);
            }
        });

        $cols = array_keys($rows[0]);
        $cmd  = UserStatusRights::getDb()->createCommand()->batchInsert(UserStatusRights::tableName(), $cols, $rows);

        /** @noinspection SqlWithoutWhere */
        $sql = <<<SQL
{$cmd->rawSql} ON CONFLICT(user_id, account_id, pipeline_id, status_id) DO UPDATE SET
  [[view]]   = EXCLUDED.[[view]],
  [[edit]]   = EXCLUDED.[[edit]],
  [[delete]] = EXCLUDED.[[delete]],
  [[export]] = EXCLUDED.[[export]];
SQL;

        $cmd = UserStatusRights::getDb()->createCommand()->setSql($sql);
        $cmd->execute();
    }

    /**
     * @param mixed[] $groups
     *
     * @throws InvalidConfigException
     * @throws InvalidModelException
     */
    public function syncGroups($groups)
    {
        $this->groupSyncer->sync([
            'accountId' => $this->cfg->accountId,
            'groups'    => $groups,
        ]);
    }
}