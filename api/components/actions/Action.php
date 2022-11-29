<?php

namespace api\components\actions;

use Yii;
use yii\helpers\ArrayHelper;
use yii\base\InvalidConfigException;

/**
 * Class Action
 *
 * @author Ivan Kudinov <i.kudinov@frostealth.ru>
 * @package strongsquirrel\crud
 */
abstract class Action extends \yii\base\Action
{
    /**
     * @var string class name of the model which will be handled by this action.
     * The model class must implement [[ActiveRecordInterface]].
     */
    public $modelClass;

    /**
     * @var callable
     * The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     */
    public $checkAccess;

    /**
     * The additional parameters that should be made available in the view.
     * The signature of the callable should be as follows,
     *
     * ```php
     * function ($action, $model = null) {
     *     // $model is the requested model instance.
     *     // If null, it means no specific model (e.g. IndexAction)
     * }
     * ```
     *
     * or array
     *
     * ```php
     * [
     *     'cities' => function () {
     *         return City::findAll();
     *     },
     *     'isGuest' => Yii::$app->user->isGuest,
     * ];
     * ```
     *
     * @var array|callable
     */
    public $params = [];

    /**
     * Initializes the object.
     * This method is invoked at the end of the constructor after the object is initialized with the
     * given configuration.
     *
     * @throws InvalidConfigException
     */
    public function init()
    {
        if (!is_array($this->params) && !is_callable($this->params)) {
            $className = get_class($this);
            throw new InvalidConfigException("$className::\$params must be an array or a callable.");
        }

        if ($this->checkAccess !== null && !is_callable($this->checkAccess)) {
            $className = get_class($this);
            throw new InvalidConfigException("$className::\$checkAccess must be a callable");
        }
    }

    /**
     * @param mixed $model
     */
    protected function checkAccess($model = null)
    {
        if (is_callable($this->checkAccess)) {
            call_user_func($this->checkAccess, $this->id, $model);
        }
    }

    /**
     * @param array  $params
     * @param mixed  $model
     *
     * @return array
     */
    protected function resolveParams(array $params, $model = null)
    {
        $result = $this->params;
        if (is_callable($result)) {
            $result = call_user_func($result, $this->id, $model);
        }

        foreach ($result as &$value) {
            if (is_callable($value)) {
                $value = call_user_func($value, $this->id, $model);
            }
        }


        return ArrayHelper::merge($result, $params);
    }

    /**
     * Get action data from user session
     *
     * @param array $keyParams
     * @return mixed
     */
    protected function getActionDataFromSession(array $keyParams = [])
    {
        $key = $this->getActionKey($keyParams);

        return Yii::$app->session->get($key);

    }

    /**
     * Set action data to user session
     *
     * @param $value
     * @param array $keyParams
     */
    protected function setActionDataToSession($value, array $keyParams = [])
    {
        $key = $this->getActionKey($keyParams);

        Yii::$app->session->set($key, $value);
    }

    /**
     * Get action key hash used for identifying url
     *
     * @param array $keyParams
     * @return string
     */
    protected function getActionKey(array $keyParams = [])
    {
        $params = $keyParams ? http_build_query($keyParams) : '';

        return md5($this->controller->id . $this->id . $params);
    }
}
