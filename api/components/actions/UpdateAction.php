<?php

namespace api\components\actions;

use api\components\responses\ErrorResponse;
use api\components\responses\SuccessResponse;
use Yii;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\rest\Action;
use yii\web\ServerErrorHttpException;

class UpdateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return array the model being updated
     * @throws ServerErrorHttpException
     */
    public function run($id = null)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        $model->scenario = $this->scenario;
        $model->load(Yii::$app->getRequest()->getBodyParams(), '');
        if ($model->save()) {
            return (new SuccessResponse($model))->asArray();
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return (new ErrorResponse($model->getFirstErrors()))->asArray();
    }
}
