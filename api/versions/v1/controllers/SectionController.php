<?php

namespace api\versions\v1\controllers;

use api\components\actions\UpdateAction;
use api\components\web\BaseApiController;
use api\models\forms\SectionReorderForm;
use app\models\search\SectionSearch;
use app\models\Section;
use yii\helpers\ArrayHelper;

class SectionController extends BaseApiController
{
    public $modelClass = Section::class;
    public $searchModelClass = SectionSearch::class;

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
                'modelClass' => SectionReorderForm::class,
                'checkAccess' => [$this, 'checkAccess'],
                'scenario' => Section::SCENARIO_REORDER,
            ],
        ]);
    }
}
