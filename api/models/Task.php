<?php

namespace app\models;

use api\components\orm\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "task".
 *
 * @property int $id
 * @property int $section_id
 * @property string|null $name
 * @property string|null $description
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property Section $section
 */
class Task extends ActiveRecord
{
    const SCENARIO_REORDER = 'reorder';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'task';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['section_id'], 'required'],
            [['section_id', 'order', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted'], 'integer'],
            [['description'], 'string'],
            [['name'], 'string', 'max' => 255],
            [['order'], 'default', 'value' => $this->getNextId(), 'on' => [static::SCENARIO_CREATE]],
            [['order'], 'required', 'on' => [static::SCENARIO_REORDER]],
            [['section_id'], 'exist', 'skipOnError' => true, 'targetClass' => Section::class, 'targetAttribute' => ['section_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'section_id' => Yii::t('app', 'Section ID'),
            'name' => Yii::t('app', 'Name'),
            'description' => Yii::t('app', 'Description'),
            'order' => Yii::t('app', 'Order'),
            'created_at' => Yii::t('app', 'Created At'),
            'created_by' => Yii::t('app', 'Created By'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'updated_by' => Yii::t('app', 'Updated By'),
            'is_deleted' => Yii::t('app', 'Is Deleted'),
        ];
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

    public function scenarios()
    {
        return array_merge(parent::scenarios(), [
            static::SCENARIO_REORDER => ['order', 'section_id'],
            static::SCENARIO_UPDATE => ['name', 'order', 'board_id']
        ]);
    }

    /**
     * Gets query for [[Section]].
     *
     * @return ActiveQuery
     */
    public function getSection()
    {
        return $this->hasOne(Section::class, ['id' => 'section_id']);
    }
}
