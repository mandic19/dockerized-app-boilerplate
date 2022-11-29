<?php

namespace api\models;

use api\components\orm\ActiveRecord;
use api\components\behaviors\image\ImageBehavior;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "image".
 *
 * @property int $id
 * @property string $original_name
 * @property string $mime_type
 * @property int $size
 * @property string $storage_type
 * @property string $storage_key
 * @property int|null $height
 * @property int|null $width
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property ImageThumb[] $imageThumbs
 */
class Image extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return array_merge(parent::behaviors(), [
            'imageable' => [
                'class' => ImageBehavior::class
            ],
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['original_name', 'mime_type', 'size', 'storage_type', 'storage_key'], 'required'],
            [['size', 'height', 'width', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted'], 'integer'],
            [['original_name', 'storage_key'], 'string', 'max' => 255],
            [['mime_type', 'storage_type'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'original_name' => Yii::t('app', 'Original Name'),
            'mime_type' => Yii::t('app', 'Mime Type'),
            'size' => Yii::t('app', 'Size'),
            'storage_type' => Yii::t('app', 'Storage Type'),
            'storage_key' => Yii::t('app', 'Storage Key'),
            'height' => Yii::t('app', 'Height'),
            'width' => Yii::t('app', 'Width'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    /**
     * Gets query for [[ImageThumbs]].
     *
     * @return ActiveQuery
     */
    public function getImageThumbs()
    {
        return $this->hasMany(ImageThumb::className(), ['image_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::class, ['image_id' => 'id']);
    }
}
