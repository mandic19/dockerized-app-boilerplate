<?php

namespace api\helpers;

use api\models\User;
use Exception;
use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Inflector;

class RbacHelper
{
    const ROLE_SUPER_ADMIN = 'super_admin';
    const ROLE_ADMIN = 'admin';

    const ROLES = [
        self::ROLE_SUPER_ADMIN => 'Super Admin',
        self::ROLE_ADMIN => 'Admin'
    ];

    /**
     * @throws Exception
     */
    public static function getRoleLabel($roleName)
    {
        if (!$roleLabel = ArrayHelper::getValue(static::ROLES, $roleName, 'Customer')) {
            return Inflector::humanize($roleName);
        }
        return $roleLabel;
    }

    public static function getCustomerUsers() {
        return User::findAll(['NOT IN', 'id', self::getBackendUserIds()]);
    }

    public static function getBackendUsers() {
        return User::findAll(['IN', 'id', self::getBackendUserIds()]);
    }

    public static function getBackendUserIds() {
        $superAdminIds = Yii::$app->authManager->getUserIdsByRole(static::ROLE_SUPER_ADMIN);
        $adminIds = Yii::$app->authManager->getUserIdsByRole(static::ROLE_ADMIN);

        return ArrayHelper::merge($adminIds, $superAdminIds);
    }
}
