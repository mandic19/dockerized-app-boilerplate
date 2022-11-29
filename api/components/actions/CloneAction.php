<?php


namespace api\components\actions;

use api\components\responses\ErrorResponse;
use api\components\responses\SuccessResponse;
use yii\base\Model;
use yii\db\ActiveRecord;
use yii\rest\Action;
use yii\web\ServerErrorHttpException;

class CloneAction extends Action
{
    /**
     * @var string the scenario to be assigned to the model before it is validated and updated.
     */
    public $scenario = Model::SCENARIO_DEFAULT;

    /**
     * Updates an existing model.
     * @param string $id the primary key of the model.
     * @return \yii\db\ActiveRecordInterface the model being updated
     * @throws ServerErrorHttpException if there is any error when updating the model
     */
    public function run($id)
    {
        /* @var $model ActiveRecord */
        $model = $this->findModel($id);

        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        /* @var $clone \yii\db\ActiveRecord */
        $clone = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $clone->attributes = $model->attributes;

        if ($clone->save()) {
            return (new SuccessResponse($clone))->asArray();
        } elseif (!$clone->hasErrors()) {
            throw new ServerErrorHttpException('Failed to clone the object for unknown reason.');
        }

        return (new ErrorResponse($clone->getFirstErrors()))->asArray();
    }
}
