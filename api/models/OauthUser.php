<?php

namespace api\models;

use filsh\yii2\oauth2server\Module;
use OAuth2\Storage\UserCredentialsInterface;
use Yii;
use yii\db\ActiveRecord;

class OauthUser extends User implements UserCredentialsInterface
{
    /**
     * @param $username
     * @param $password
     * @return bool
     */
    public function checkUserCredentials($username, $password)
    {
        /* @var $user User */
        $user = $this->findByUsernameOrEmail($username, true);
        if (empty($user)) {
            return false;
        }

        return $user->validatePassword($password);
    }

    /**
     * @param string $username
     * @return array
     */
    public function getUserDetails($username)
    {
        $user = $this->findByUsernameOrEmail($username, true);
        return ['user_id' => $user->getId()];
    }

    public function fields()
    {
        $fields = parent::fields(); // TODO: Change the autogenerated stub

        unset($fields['password_hash']);
        unset($fields['password_reset_token']);
        unset($fields['verification_token']);
        unset($fields['auth_key']);
        unset($fields['created_at']);
        unset($fields['created_by']);
        unset($fields['updated_at']);
        unset($fields['updated_by']);
        unset($fields['is_deleted']);

        return $fields;
    }
}
