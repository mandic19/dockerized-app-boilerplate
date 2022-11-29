<?php

namespace api\components\actions;

class ViewAction extends \yii\rest\Action
{
    public function run($id = null)
    {
        $model = $this->findModel($id);
        if ($this->checkAccess) {
            call_user_func($this->checkAccess, $this->id, $model);
        }

        return $model;
    }
}
