<?php

namespace api\components\web;

use Yii;
use api\components\actions\CreateAction;
use api\components\actions\SearchAction;
use api\components\actions\UpdateAction;
use api\components\filters\CorsFilter;
use api\components\filters\OAuth2AccessFilter;
use api\components\orm\ActiveRecord;
use filsh\yii2\oauth2server\filters\auth\CompositeAuth;
use filsh\yii2\oauth2server\filters\ErrorToExceptionFilter;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\filters\auth\QueryParamAuth;
use yii\filters\ContentNegotiator;
use yii\filters\RateLimiter;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\rest\ActiveController;
use yii\rest\DeleteAction;
use yii\rest\IndexAction;
use yii\rest\OptionsAction;
use yii\rest\ViewAction;
use yii\web\NotFoundHttpException;

abstract class BaseApiController extends ActiveController
{
    public $findModel = null;
    public $modelClass;
    public $searchModelClass;
    public $guestActions = [];

    public $createScenario = ActiveRecord::SCENARIO_CREATE;
    public $updateScenario = ActiveRecord::SCENARIO_UPDATE;

    /**
     * Override parent behaviors to ensure certain order on filters
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'corsFilter' => [
                'class' => CorsFilter::class
            ],
            'verbFilter' => [
                'class' => VerbFilter::class,
                'actions' => $this->verbs(),
            ],
            'rateLimiter' => [
                'class' => RateLimiter::class,
            ],
            'oauth2access' => [
                'class' => OAuth2AccessFilter::class
            ],
            'contentNegotiator' => [
                'class' => ContentNegotiator::class,
                'formats' => Yii::$app->params['formats']
            ],
            'authenticator' => [
                'class' => CompositeAuth::class,
                'except' => ArrayHelper::merge(['options'], $this->guestActions),
                'authMethods' => [
                    ['class' => HttpBearerAuth::class],
                    ['class' => QueryParamAuth::class, 'tokenParam' => 'access_token'],
                ]
            ],
            'exceptionFilter' => [
                'class' => ErrorToExceptionFilter::class
            ],
            'access' => [
                'class' => AccessControl::class,
                'rules' => $this->accessRules(),
                'ruleConfig' => [
                    'class' => 'yii\filters\AccessRule'
                ]
            ],
        ];
    }

    /**
     * @return array the access rules
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
                'roles' => ['*'],
            ]
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'index' => [
                'class' => SearchAction::class,
                'modelClass' => $this->searchModelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'view' => [
                'class' => ViewAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'create' => [
                'class' => CreateAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->createScenario,
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => $this->updateScenario,
            ],
            'delete' => [
                'class' => DeleteAction::class,
                'modelClass' => $this->modelClass,
                'checkAccess' => [$this, 'checkAccess'],
            ],
            'options' => [
                'class' => OptionsAction::class
            ]
        ];
    }

    protected function findModel($id)
    {
        if ($this->findModel !== null) {
            return call_user_func($this->findModel, $id, $this);
        }

        /* @var $modelClass ActiveRecord */
        $modelClass = $this->modelClass;
        $model = $modelClass::findOne((int)$id);

        if (empty($model)) {
            throw new NotFoundHttpException("Object not found: $id");
        }

        return $model;
    }
}
