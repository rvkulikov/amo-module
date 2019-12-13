<?php

namespace rvkulikov\amo\module\services\customField\sync;

use rvkulikov\amo\module\exceptions\InvalidModelException;
use rvkulikov\amo\module\helpers\ModelHelper;
use rvkulikov\amo\module\models\Account;
use rvkulikov\amo\module\models\CustomField;
use rvkulikov\amo\module\services\util\safeDelete\SafeDeleter_Interface;
use yii\base\Component;
use yii\base\InvalidConfigException;
use yii\db\Exception;
use yii\db\JsonExpression;
use yii\helpers\ArrayHelper;

/**
 *
 */
class CustomFieldSyncer_Impl extends Component implements CustomFieldSyncer_Interface
{
    /** @var CustomFieldSyncer_Cfg */
    private $cfg;
    /** @var Account */
    private $account;
    /** @var SafeDeleter_Interface */
    private $safeDeleter;

    /**
     * {@inheritDoc}
     */
    public function __construct(SafeDeleter_Interface $safeDeleter, $config = [])
    {
        parent::__construct($config);
        $this->safeDeleter = $safeDeleter;
    }

    /**
     * @param array|CustomFieldSyncer_Cfg $cfg
     * @throws InvalidModelException
     * @throws InvalidConfigException
     * @throws Exception
     */
    public function sync($cfg)
    {
        $this->cfg = ModelHelper::ensure($cfg, CustomFieldSyncer_Cfg::class);
        $this->account = Account::findOne(['id' => $this->cfg->accountId]);

        $fields = $this->cfg->customFields;
        $rows = array_merge(
            $this->buildStandard('contacts', ArrayHelper::getValue($fields, 'contacts', [])),
            $this->buildStandard('leads', ArrayHelper::getValue($fields, 'leads', [])),
            $this->buildStandard('companies', ArrayHelper::getValue($fields, 'companies', [])),
            $this->buildStandard('customers', ArrayHelper::getValue($fields, 'customers', [])),
            $this->buildCatalog('catalogs', ArrayHelper::getValue($fields, 'catalogs', []))
        );
        $cols = array_keys($rows[0]);

        $cmd = CustomField::getDb()->createCommand()->batchInsert(CustomField::tableName(), $cols, $rows);
        $sql = <<<SQL
{$cmd->rawSql} ON CONFLICT (entity, id) DO UPDATE SET
  [[account_id]]    = EXCLUDED.account_id,
  [[catalog_id]]    = EXCLUDED.catalog_id,
  [[name]]          = EXCLUDED.name,
  [[field_type]]    = EXCLUDED.field_type,
  [[sort]]          = EXCLUDED.sort,
  [[code]]          = EXCLUDED.code,
  [[is_multiple]]   = EXCLUDED.is_multiple,
  [[is_system]]     = EXCLUDED.is_system,
  [[is_editable]]   = EXCLUDED.is_editable,
  [[is_required]]   = EXCLUDED.is_required,
  [[is_deletable]]  = EXCLUDED.is_deletable,
  [[is_visible]]    = EXCLUDED.is_visible,
  [[params]]        = EXCLUDED.params,
  [[enums]]         = EXCLUDED.enums,
  [[values_tree]]   = EXCLUDED.values_tree;
SQL;

        $cmd = CustomField::getDb()->createCommand()->setSql($sql);
        $cmd->execute();

        $this->safeDeleter->delete([
            'definition' => CustomField::class,
            'strategy'   => 'soft',
            'rows'       => $rows,
            'where'      => ['account_id' => $this->account->id],
        ]);
    }

    /**
     * @param $entity
     * @param $fields
     * @return array
     */
    protected function buildStandard($entity, $fields)
    {
        if (empty($fields)) {
            return [];
        }

        $rows = [];
        foreach ($fields as $field) {
            $rows[] = [
                'account_id'   => $this->account->id,
                'id'           => ArrayHelper::getValue($field, 'id'),
                'entity'       => $entity,
                'catalog_id'   => null,
                'name'         => ArrayHelper::getValue($field, 'name'),
                'field_type'   => ArrayHelper::getValue($field, 'field_type'),
                'sort'         => ArrayHelper::getValue($field, 'sort'),
                'code'         => ArrayHelper::getValue($field, 'code'),
                'is_multiple'  => ArrayHelper::getValue($field, 'is_multiple'),
                'is_system'    => ArrayHelper::getValue($field, 'is_system'),
                'is_editable'  => ArrayHelper::getValue($field, 'is_editable'),
                'is_required'  => ArrayHelper::getValue($field, 'is_required'),
                'is_deletable' => ArrayHelper::getValue($field, 'is_deletable'),
                'is_visible'   => ArrayHelper::getValue($field, 'is_visible'),
                'params'       => new JsonExpression(ArrayHelper::getValue($field, 'params')),
                'enums'        => new JsonExpression(ArrayHelper::getValue($field, 'enums')),
                'values_tree'  => new JsonExpression(ArrayHelper::getValue($field, 'values_tree')),
            ];
        }

        return $rows;
    }

    /**
     * @param $entity
     * @param $catalogs
     * @return array
     */
    protected function buildCatalog($entity, $catalogs)
    {
        if (empty($catalogs)) {
            return [];
        }

        $rows = [];
        foreach ($catalogs as $catalogId => $fields) {
            foreach ($fields as $field) {
                $rows[] = [
                    'account_id'   => $this->account->id,
                    'id'           => ArrayHelper::getValue($field, 'id'),
                    'entity'       => $entity,
                    'catalog_id'   => $catalogId,
                    'name'         => ArrayHelper::getValue($field, 'name'),
                    'field_type'   => ArrayHelper::getValue($field, 'field_type'),
                    'sort'         => ArrayHelper::getValue($field, 'sort'),
                    'code'         => ArrayHelper::getValue($field, 'code'),
                    'is_multiple'  => ArrayHelper::getValue($field, 'is_multiple'),
                    'is_system'    => ArrayHelper::getValue($field, 'is_system'),
                    'is_editable'  => ArrayHelper::getValue($field, 'is_editable'),
                    'is_required'  => ArrayHelper::getValue($field, 'is_required'),
                    'is_deletable' => ArrayHelper::getValue($field, 'is_deletable'),
                    'is_visible'   => ArrayHelper::getValue($field, 'is_visible'),
                    'params'       => new JsonExpression(ArrayHelper::getValue($field, 'params')),
                    'enums'        => new JsonExpression(ArrayHelper::getValue($field, 'enums')),
                    'values_tree'  => new JsonExpression(ArrayHelper::getValue($field, 'values_tree')),
                ];
            }
        }

        return $rows;
    }
}