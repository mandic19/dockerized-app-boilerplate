<?php

namespace api\versions\v1\controllers;

use api\components\actions\UpdateAction;
use api\components\web\BaseApiController;
use api\models\forms\TaskReorderForm;
use app\models\search\TaskSearch;
use app\models\Section;
use app\models\Task;
use yii\helpers\ArrayHelper;

class TaskController extends BaseApiController
{
    public $modelClass = Task::class;
    public $searchModelClass = TaskSearch::class;

    public $guestActions = ['index', 'view', 'create', 'update', 'delete', 'reorder'];

    /**
     * @return array the access rules
     */
    public function accessRules()
    {
        return [
            [
                'allow' => true,
            ]
        ];
    }

    public function actions()
    {
        return ArrayHelper::merge(parent::actions(), [
            'reorder' => [
                'class' => UpdateAction::class,
                'modelClass' => TaskReorderForm::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => Task::SCENARIO_REORDER,
            ],
        ]);
    }
}
