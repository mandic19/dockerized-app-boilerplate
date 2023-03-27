<?php

namespace api\versions\v1\controllers;

use api\components\web\BaseApiController;
use app\models\Board;
use app\models\extended\BoardWithSections;
use app\models\search\BoardSearch;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;
use yii\rest\ViewAction;

class BoardController extends BaseApiController
{
    public $modelClass = Board::class;
    public $searchModelClass = BoardSearch::class;

    public $guestActions = ['index', 'view', 'create', 'update', 'delete'];

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
        return array_merge(parent::actions(), [
            'view' => [
                'class' => ViewAction::class,
                'modelClass' => BoardWithSections::class,
                'checkAccess' => [$this, 'checkAccess'],
            ],
        ]);
    }
}
