<?php

namespace api\components\actions;

use api\components\responses\ErrorResponse;
use api\components\responses\SuccessResponse;
use Yii;
use yii\base\Model;
use yii\web\ServerErrorHttpException;

/**
 * CreateAction implements the API endpoint for creating a new model from the given data.
 *
 * For more details and usage information on CreateAction, see the [guide article on rest controllers](guide:rest-controllers).
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class CreateAction extends Action
{
    /**
     * @var string the scenario to be assigned to the new model before it is validated and saved.
     */
    public $scenario = Model::SCENARIO_DEFAULT;
    /**
     * @var string the name of the view action. This property is need to create the URL when the model is successfully created.
     */
    public $viewAction = 'view';

    public $formName = '';
    public $scope = null;


    /**
     * Creates a new model.
     * @return \yii\db\ActiveRecordInterface the model newly created
     * @throws ServerErrorHttpException if there is any error when creating the model
     */
    public function run()
    {
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id);
        }

        /* @var $model \yii\db\ActiveRecord */
        $model = new $this->modelClass([
            'scenario' => $this->scenario,
        ]);

        $params = Yii::$app->getRequest()->getBodyParams();

        $model->load($params, $this->formName);

        $user = Yii::$app->user->getIdentity();
        if (is_callable($this->scope)) {
            $params = call_user_func_array($this->scope, [$model, $user, $params]);
        }

        if ($model->save()) {
            return (new SuccessResponse($model))->asArray();
        } elseif (!$model->hasErrors()) {
            throw new ServerErrorHttpException('Failed to create the object for unknown reason.');
        }

        return (new ErrorResponse($model->getFirstErrors()))->asArray();
    }
}
