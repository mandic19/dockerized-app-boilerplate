<?php

namespace api\versions\v1\controllers;

use api\components\actions\UpdateAction;
use api\components\actions\ViewAction;
use api\components\orm\ActiveRecord;
use api\components\web\BaseApiController;
use api\models\forms\RegistrationForm;
use api\models\User;
use Yii;
use yii\helpers\ArrayHelper;
use yii\rest\OptionsAction;
use api\components\actions\CreateAction;

class UserController extends BaseApiController
{
    public $modelClass = User::class;
    public $guestActions = ['signup'];

    public function accessRules()
    {
        return ArrayHelper::merge(parent::accessRules(), [
            [
                'actions' => ['update', 'info', 'options'],
                'allow' => '@',
            ],
            [
                'actions' => ['signup'],
                'allow' => true
            ]
        ]);
    }

    public function actions()
    {
        return [
            'options' => [
                'class' => OptionsAction::class
            ],
            'signup' => [
                'class' => CreateAction::class,
                'modelClass' => RegistrationForm::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => ActiveRecord::SCENARIO_CREATE
            ],
            'update' => [
                'class' => UpdateAction::class,
                'modelClass' => RegistrationForm::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => RegistrationForm::SCENARIO_UPDATE_INFO,
                'findModel' => function () {
                    return RegistrationForm::findOne(Yii::$app->user->id);
                }
            ],
            'info' => [
                'class' => ViewAction::class,
                'modelClass' => RegistrationForm::class,
                'checkAccess' => [$this, 'checkAccess'],
                'findModel' => function () {
                    return RegistrationForm::findOne(null);
                }
            ]
        ];
    }
}
