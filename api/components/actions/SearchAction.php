<?php
/**
 *
 * Index.php
 *
 * Date: 01/04/14
 * Time: 20:57
 * @author Antonio Ramirez <amigo.cobos@gmail.com>
 * @link http://www.ramirezcobos.com/
 * @link http://www.2amigos.us/
 */

namespace api\components\actions;

use Yii;
use yii\rest\Action;

class SearchAction extends Action
{
    public $searchScenario = 'default';
    public $formName = null;
    public $scope = null;

    public function run()
    {
        ini_set('memory_limit','1024M');
        $class = $this->modelClass;

        $searchModel = new $class(["scenario" => $this->searchScenario]);

        $params = Yii::$app->request->getQueryParams();
        $user = Yii::$app->user->getIdentity();

        if (is_callable($this->scope)) {
            $params = call_user_func_array($this->scope, [$searchModel, $user, $params]);
        }

        $searchModel->load($params, $this->formName);

        return $searchModel->search($params);
    }
} 
