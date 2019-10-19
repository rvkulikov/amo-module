<?php
namespace rvkulikov\amo\module\helpers;

use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

class ModelHelper
{
    /**
     * @param Model|mixed[] $data
     * @param string        $class
     *
     * @return Model
     * @throws InvalidConfigException
     */
    public static function ensure($data, $class)
    {
        if ($data instanceof $class) {
            return $data;
        }

        if (is_array($data)) {
            /** @var Model $model */
            $model = Yii::createObject($class);
            $key   = array_key_exists($model->formName(), $data)
                ? $model->formName()
                : '';

            $model->load($data, $key);

            return $model;
        }

        throw new InvalidConfigException('$data is not an array or $class instance');
    }
}