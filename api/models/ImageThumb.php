<?php

namespace api\models;

use api\components\orm\ActiveRecord;
use api\components\behaviors\image\ImageBehavior;
use Yii;

/**
 * This is the model class for table "image_thumb".
 *
 * @property int $id
 * @property int $image_id
 * @property string $spec_key
 * @property string $storage_type
 * @property string $storage_key
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property Image $image
 */
class ImageThumb extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'image_thumb';
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
            [['image_id', 'spec_key', 'storage_type', 'storage_key'], 'required'],
            [['image_id', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted'], 'integer'],
            [['spec_key', 'storage_type'], 'string', 'max' => 45],
            [['storage_key'], 'string', 'max' => 255],
            [['image_id'], 'exist', 'skipOnError' => true, 'targetClass' => Image::className(), 'targetAttribute' => ['image_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'image_id' => Yii::t('app', 'Image ID'),
            'spec_key' => Yii::t('app', 'Spec Key'),
            'storage_type' => Yii::t('app', 'Storage Type'),
            'storage_key' => Yii::t('app', 'Storage Key'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    /**
     * Gets query for [[Image]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImage()
    {
        return $this->hasOne(Image::className(), ['id' => 'image_id']);
    }
}
