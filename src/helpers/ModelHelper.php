<?php

namespace rvkulikov\amo\module\helpers;

use Closure;
use rvkulikov\amo\module\exceptions\InvalidModelException;
use Yii;
use yii\base\InvalidConfigException;
use yii\base\Model;

/**
 *
 */
class ModelHelper
{
    /**
     * @param Model|mixed[] $data
     * @param string $class
     * @param bool $validate
     *
     * @return Model
     * @throws InvalidConfigException
     * @throws InvalidModelException
     */
    public static function ensure($data, $class, $validate = false)
    {
        if ($data instanceof $class) {
            $model = $data;
        } elseif ($data instanceof Closure || is_callable($data)) {
            $model = call_user_func($data);
        } elseif (is_array($data)) {
            /** @var Model $model */
            $model = Yii::createObject($class);
            $key = array_key_exists($model->formName(), $data)
                ? $model->formName()
                : '';

            $model->load($data, $key);
        } else {
            throw new InvalidConfigException('$data is not an array or $class instance');
        }

        if ($validate && !$model->validate()) {
            throw new InvalidModelException($model);
        }

        return $model;
    }
}