<?php

namespace api\components;

use api\models\User;
use Exception;
use yii\helpers\ArrayHelper;

/**
 * Class WebUser
 * @package api\components
 *
 * @property User $identity
 */
class WebUser extends \yii\web\User
{
    protected $fullName;

    /**
     * @throws Exception
     */
    public function getFullName()
    {
        if (empty($this->fullName)) {
                $this->fullName = ArrayHelper::getValue($this, 'identity.fullName');
        }

        return $this->fullName;
    }
}
