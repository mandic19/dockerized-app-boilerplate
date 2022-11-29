<?php

use yii\db\Migration;

/**
 * Class m210925_210650_insert_roles
 */
class m210925_210650_insert_roles extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $auth = Yii::$app->authManager;

        $superAdminRole = $auth->createRole('super_admin');
        $superAdminRole->description = 'Super Admin';

        $adminRole = $auth->createRole('admin');
        $adminRole->description = 'Admin';

        $auth->add($superAdminRole);
        $auth->add($adminRole);
        $auth->addChild($superAdminRole, $adminRole);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->authManager;

        $superAdminRole = $auth->createRole('super_admin');
        $adminRole = $auth->createRole('admin');

        $auth->removeChild($superAdminRole, $adminRole);
        $auth->remove($superAdminRole);
        $auth->remove($adminRole);
    }
}
