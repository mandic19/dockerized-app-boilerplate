<?php

namespace api\models;

use api\components\behaviors\RbacBehavior;
use api\components\orm\ActiveRecord;
use api\helpers\BaseHelper;
use filsh\yii2\oauth2server\Module;
use Yii;
use yii\base\NotSupportedException;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\web\IdentityInterface;

/**
 * User model
 *
 * @property integer $id
 * @property string $username
 * @property string $role
 * @property string $password_hash
 * @property string $password_reset_token
 * @property string $verification_token
 * @property string $email
 * @property string $auth_key
 * @property integer $status
 * @property string $first_name
 * @property string $last_name
 * @property string $address
 * @property string $city
 * @property string $country
 * @property integer $zip
 * @property string $phone
 * @property integer $created_at
 * @property integer $created_by
 * @property integer $updated_at
 * @property integer $updated_by
 * @property integer $is_deleted
 * @property string $password write-only password
 *
 * @property string $fullName
 */
class User extends ActiveRecord implements IdentityInterface
{
    const STATUS_INACTIVE = 9;
    const STATUS_ACTIVE = 10;

    private $_role;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'timestamp' => TimestampBehavior::class,
            'blamable' => BlameableBehavior::class,
            'RbacBehavior' => RbacBehavior::class
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name', 'username', 'role', 'address', 'city', 'country', 'zip', 'phone'], 'string'],
            ['status', 'default', 'value' => self::STATUS_ACTIVE],
            ['status', 'in', 'range' => [self::STATUS_ACTIVE, self::STATUS_INACTIVE]],
            [['email'], 'email'],
            [['email', 'username'], 'checkUniqueness']
        ];
    }

    public function checkUniqueness($attribute, $params)
    {
        /* @var $user User */
        $user = User::findByUsernameOrEmail($this->{$attribute});

        if (!empty($user) && $user->id != $this->id) {
            $attributeLabel = $this->getAttributeLabel($attribute);
            $this->addError($attribute, "{$attributeLabel} is already used");
        }
    }

    /**
     * {@inheritdoc}
     */
    public static function findIdentity($id)
    {
        return static::findOne(['id' => $id, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * @inheritdoc
     */
    public static function findIdentityByAccessToken($token, $type = null)
    {
        /** @var Module $module */
        $module = Yii::$app->getModule('oauth2');
        $token = $module->getServer()->getResourceController()->getToken();

        return !empty($token['user_id'])
            ? static::findIdentity($token['user_id'])
            : null;
    }

    /**
     * Finds user by username
     *
     * @param string $username
     * @return static|null
     */
    public static function findByUsername($username)
    {
        return static::findOne(['username' => $username, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username
     *
     * @param string $email
     * @return static|null
     */
    public static function findByEmail($email)
    {
        return static::findOne(['email' => $email, 'status' => self::STATUS_ACTIVE]);
    }

    /**
     * Finds user by username or email
     *
     * @param string $key
     * @param bool $onlyActive
     * @return array|\yii\db\ActiveRecord|null
     *
     */
    public static function findByUsernameOrEmail($key, $onlyActive = false)
    {
        $query = static::find()->where(['OR', ['username' => $key], ['email' => $key]]);

        if(!$onlyActive) {
            return $query->one();
        }

        return $query->andWhere(['status' => self::STATUS_ACTIVE])->one();
    }

    /**
     * Finds user by password reset token
     *
     * @param string $token password reset token
     * @return static|null
     */
    public static function findByPasswordResetToken($token)
    {
        if (!static::isPasswordResetTokenValid($token)) {
            return null;
        }

        return static::findOne([
            'password_reset_token' => $token,
            'status' => self::STATUS_ACTIVE,
        ]);
    }

    /**
     * Finds user by verification email token
     *
     * @param string $token verify email token
     * @return static|null
     */
    public static function findByVerificationToken($token) {
        return static::findOne([
            'verification_token' => $token,
            'status' => self::STATUS_INACTIVE
        ]);
    }

    /**
     * Finds out if password reset token is valid
     *
     * @param string $token password reset token
     * @return bool
     */
    public static function isPasswordResetTokenValid($token)
    {
        if (empty($token)) {
            return false;
        }

        $timestamp = (int) substr($token, strrpos($token, '_') + 1);
        $expire = Yii::$app->params['user.passwordResetTokenExpire'];
        return $timestamp + $expire >= time();
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->getPrimaryKey();
    }

    /**
     * {@inheritdoc}
     */
    public function getAuthKey()
    {
        return $this->auth_key;
    }

    /**
     * {@inheritdoc}
     */
    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /**
     * Validates password
     *
     * @param string $password password to validate
     * @return bool if password provided is valid for current user
     */
    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password_hash);
    }

    /**
     * Generates password hash from password and sets it to the model
     *
     * @param string $password
     */
    public function setPassword($password)
    {
        $this->password_hash = Yii::$app->security->generatePasswordHash($password);
    }

    /**
     * Generates "remember me" authentication key
     */
    public function generateAuthKey()
    {
        $this->auth_key = Yii::$app->security->generateRandomString();
    }

    /**
     * Generates new password reset token
     */
    public function generatePasswordResetToken()
    {
        $this->password_reset_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Generates new token for email verification
     */
    public function generateEmailVerificationToken()
    {
        $this->verification_token = Yii::$app->security->generateRandomString() . '_' . time();
    }

    /**
     * Removes password reset token
     */
    public function removePasswordResetToken()
    {
        $this->password_reset_token = null;
    }

    public function getRole()
    {
        if ($this->_role) {
            return $this->_role;
        }

        if (empty($this->getOldAttributes()['role'])) {
            $this->getBehavior('RbacBehavior')->initializeRole();
            $this->_role = $this->getOldAttributes()['role'];
        }

        return $this->_role;
    }

    public function getFullName() {
        return BaseHelper::formatToCharSeparatedString([$this->first_name, $this->last_name], ' ');
    }
}
