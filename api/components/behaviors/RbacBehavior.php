<?php

namespace api\components\behaviors;

use api\components\orm\ActiveRecord;
use Exception;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;
use yii\helpers\ArrayHelper;
use yii\rbac\DbManager;
use yii\rbac\Role;

/**
 * Class RbacBehavior
 * @package api\components\behaviors
 *
 * @property ActiveRecord $owner
 * @property string $role
 *
 */
class RbacBehavior extends Behavior
{
    public $role;

    private $_roleObject;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_INSERT => 'assignRole',
            BaseActiveRecord::EVENT_AFTER_UPDATE => 'assignRole'
        ];
    }

    public function initializeRole($role = null)
    {
        if ($role === null) {
            $role = $this->getRole();
        }

        $this->role = ArrayHelper::getValue($role, 'name');
        $oldAttributes = $this->owner->getOldAttributes();
        $oldAttributes['role'] = $this->role;

        $this->owner->setOldAttributes($oldAttributes);
    }

    public function getRole()
    {
        if (!empty($this->_roleObject)) {
            return $this->_roleObject;
        }

        $roles = Yii::$app->getAuthManager()->getRolesByUser($this->owner->id);

        if (!$this->owner->isNewRecord && count($roles) > 1) {
            $count = count($roles);

            throw new Exception("Invalid number of roles assigned to user({$count})");
        }

        $roleObject = $roles ? array_pop($roles) : '';
        $this->_roleObject = $roleObject;

        $oldAttributes = $this->owner->getOldAttributes();
        if (empty($oldAttributes['role'])) {
            $this->initializeRole($roleObject);
        }

        return $roleObject;
    }

    public function assignRole()
    {
        if (empty($this->role)) {
            return true;
        }

        /**
         * @var DbManager $auth
         * @var Role $role
         */
        $auth = Yii::$app->getAuthManager();

        $role = $auth->getRole($this->role);

        if (empty($role)) {
            throw new Exception('Could not assign non existing role.');
        }

        if ($auth->getAssignment($role->name, $this->owner->id)) {
            return $role;
        }

        foreach ($auth->getRoles() as $item) {
            $auth->revoke($item, $this->owner->id);
        }

        return $auth->assign($role, $this->owner->id);
    }

    public function assignCustomRole($role)
    {
        $this->role = $role;
        return $this->assignRole();
    }
}
