<?php

namespace api\models\forms;

use Yii;
use yii\helpers\ArrayHelper;
use api\models\User;

/**
 * Class RegistrationForm
 * @package api\models\forms
 *
 * @property string $password
 * @property string $password_repeat
 * @property string $role
 *
 */
class RegistrationForm extends User
{
    public $password;
    public $password_repeat;
    public $role;

    const SCENARIO_UPDATE_INFO = 'scenario-update-info';

    public function getBaseName()
    {
        return Yii::t('app', 'User');
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['email', 'username', 'password', 'password_repeat', 'role'], 'required', 'on' => [static::SCENARIO_CREATE]],
            [['password'], 'string', 'min' => Yii::$app->params['user.passwordMinLength']],
            [['password'], 'match', 'pattern' => Yii::$app->params['pattern']['letter'],
                'message' => Yii::t('app', 'New Password must contain at least 1 letter.')
            ],
            [['password'], 'match', 'pattern' => Yii::$app->params['pattern']['digit'],
                'message' => Yii::t('app', 'New Password must contain at least 1 number.')
            ],
            [['password'], 'match', 'pattern' => Yii::$app->params['pattern']['specialChar'],
                'message' => Yii::t('app', 'New Password must contain at least 1 special character.')
            ],
            [['password'], 'compare', 'compareAttribute' => 'password_repeat', 'operator' => '==', 'enableClientValidation' => false],
            [['role'], 'string']
        ]);
    }

    public function beforeValidate()
    {
        $this->username = empty($this->username) ? $this->email : $this->username;
        $this->status = $this->status ?: static::STATUS_ACTIVE;

        return parent::beforeValidate();
    }

    public function scenarios()
    {
        $attr = $this->getAllAttributeNames();
        return ArrayHelper::merge(parent::scenarios(), [
            self::SCENARIO_UPDATE => array_diff($attr, ['email', 'username', 'password', 'status'])
        ]);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->validate()) {
            return false;
        }

        if (!empty($this->password)) {
            $this->setPassword($this->password);
        }

        if ($this->isNewRecord) {
            $this->generateAuthKey();
        }

        $transaction = Yii::$app->db->beginTransaction();

        if (!parent::save($runValidation, $attributeNames)) {
            $transaction->rollBack();
            return false;
        }

        if (!empty($this->role)) {
            if (!$this->assignCustomRole($this->role)) {
                $transaction->rollBack();
                $this->addError('role', Yii::t('app', 'Failed while assigning role.'));
                return false;
            }
        }

        $transaction->commit();
        return true;
    }
}
