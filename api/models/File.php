<?php

namespace api\models;

use api\components\behaviors\FileBehavior;
use api\components\orm\ActiveRecord;
use Yii;
use yii\helpers\ArrayHelper;
use yii\web\UploadedFile;

/**
 * This is the model class for table "file".
 *
 * @property int $id
 * @property int|null $image_id
 * @property string|null $original_name
 * @property string|null $mime_type
 * @property string|null $storage_type
 * @property string|null $storage_key
 * @property int|null $size
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_deleted
 */
class File extends ActiveRecord
{
    const SCENARIO_FILE_UPLOAD = 'scenario_file_upload';

    public $file;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['image_id', 'size', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted'], 'integer'],
            [['original_name', 'storage_key'], 'string', 'max' => 255],
            [['mime_type', 'storage_type'], 'string', 'max' => 45],
        ];
    }

    public function validateFile($attribute)
    {
        if (!empty($this->file)) {
            return true;
        }

        $file = UploadedFile::getInstance($this, $attribute);

        if (empty($file)) {
            $this->addError($attribute, 'File can not be empty!');
            return false;
        }

        return true;
    }

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            self::SCENARIO_FILE_UPLOAD => ['*']
        ]);
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'fileable' => [
                'class' => FileBehavior::class,
                'thumbImageIdAttribute' => 'image_id',
            ]
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'image_id' => Yii::t('app', 'Image ID'),
            'original_name' => Yii::t('app', 'Original Name'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'storage_type' => Yii::t('app', 'Storage Type'),
            'storage_key' => Yii::t('app', 'Storage Key'),
            'size' => Yii::t('app', 'Size'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }
}
