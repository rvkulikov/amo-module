<?php
namespace rvkulikov\amo\module\exceptions;

use Exception;
use yii\base\Model;
use yii\helpers\Json;
use yii\web\HttpException;

/**
 *
 */
class InvalidModelException extends HttpException
{
    /**
     * @var Model
     */
    public $model;

    /**
     * {@inheritDoc}
     */
    public function __construct(Model $model, $message = null, $code = 0, Exception $previous = null)
    {
        $this->model = $model;
        $message     = $message ?? $this->getDefaultMessage();
        parent::__construct(400, $message, $code, $previous);
    }

    /**
     * @return string
     */
    public function getDefaultMessage()
    {
        $error   = Json::errorSummary($this->model);
        $error   = Json::decode($error);
        $error   = Json::encode($error, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT);
        $class   = get_class($this->model);
        $message = "Model {$class} is invalid:\n{$error}";

        return trim($message);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "Model is invalid";
    }
}