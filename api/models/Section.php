<?php

namespace app\models;

use api\components\orm\ActiveRecord;
use Yii;
use yii\db\ActiveQuery;

/**
 * This is the model class for table "section".
 *
 * @property int $id
 * @property int $board_id
 * @property string|null $name
 * @property int|null $order
 * @property int|null $created_at
 * @property int|null $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property int|null $is_deleted
 *
 * @property Board $board
 * @property Task[] $tasks
 */
class Section extends ActiveRecord
{
    const SCENARIO_REORDER = 'reorder';

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'section';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['board_id'], 'required'],
            [['board_id', 'order', 'created_at', 'created_by', 'updated_at', 'updated_by', 'is_deleted'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['order'], 'default', 'value' => $this->getNextOrder(), 'on' => [static::SCENARIO_CREATE]],
            [['order'], 'required', 'on' => [static::SCENARIO_REORDER]],
            [['board_id'], 'exist', 'skipOnError' => true, 'targetClass' => Board::class, 'targetAttribute' => ['board_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'board_id' => Yii::t('app', 'Board ID'),
            'name' => Yii::t('app', 'Name'),
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
            static::SCENARIO_REORDER => ['order'],
            static::SCENARIO_UPDATE => ['name', 'order', 'board_id']
        ]);
    }

    private function getNextOrder() {
        return self::find()->max('section.order') + 1;
    }

    /**
     * Gets query for [[Board]].
     *
     * @return ActiveQuery
     */
    public function getBoard()
    {
        return $this->hasOne(Board::class, ['id' => 'board_id']);
    }

    /**
     * Gets query for [[Tasks]].
     *
     * @return ActiveQuery
     */
    public function getTasks()
    {
        return $this->hasMany(Task::class, ['section_id' => 'id']);
    }
}
