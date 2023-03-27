<?php

namespace app\models;

use Yii;
use api\components\orm\ActiveRecord;
use yii\behaviors\SluggableBehavior;
use yii\db\ActiveQuery;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "board".
 *
 * @property int $id
 * @property string|null $name
 * @property string|null $slug
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property Section[] $sections
 */
class Board extends ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'board';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted'], 'integer'],
            [['name', 'slug'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'slug' => Yii::t('app', 'Slug'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
    }

    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            [
                'class' => SluggableBehavior::class,
                'attribute' => 'name',
                'slugAttribute' => 'slug',
            ],
        ]);
    }

    public function fields()
    {
        $fields = parent::fields();

        unset($fields['created_at']);
        unset($fields['created_by']);
        unset($fields['updated_at']);
        unset($fields['updated_by']);
        unset($fields['is_deleted']);

        return $fields;
    }

    /**
     * Gets query for [[Sections]].
     *
     * @return ActiveQuery
     */
    public function getSections()
    {
        return $this->hasMany(Section::class, ['board_id' => 'id'])->orderBy(['order' => SORT_ASC]);
    }
}
