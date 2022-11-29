<?php

namespace api\components\orm;

use Yii;
use yii\base\Exception;
use yii\db\Expression;
use yii\helpers\ArrayHelper;
use yii\db\BaseActiveRecord;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use yii\helpers\Inflector;
use yii\helpers\StringHelper;

/**
 * @property mixed _propertyAttribute
 * @inheritdoc
 */
class ActiveRecord extends \yii\db\ActiveRecord
{
    protected static $_createdByAttribute = 'created_by';
    protected static $_updatedByAttribute = 'updated_by';
    protected static $_createdAtAttribute = 'created_at';
    protected static $_updatedAtAttribute = 'updated_at';
    protected static $_isDeletedAttribute = 'is_deleted';

    protected static $_scopeAttribute = '';
    protected static $_cascadeDeleteRelations = [];


    const BLAMEABLE_BEHAVIOR_ID = 'blameable';
    const TIMESTAMP_BEHAVIOR_ID = 'timestamp';

    const SCENARIO_CREATE = 'create';
    const SCENARIO_UPDATE = 'update';

    public static function createObject($params = [])
    {
        $model = new static();
        $validAttributes = $model->getAllAttributeNames();
        $validParams = array_filter(
            $params,
            function ($key) use ($validAttributes) {
                return in_array($key, $validAttributes);
            },
            ARRAY_FILTER_USE_KEY
        );

        return new static($validParams);
    }

    public function scenarios()
    {
        $allAttributes = $this->getAllAttributeNames();

        return ArrayHelper::merge(parent::scenarios(), [
            static::SCENARIO_CREATE => $allAttributes,
            static::SCENARIO_UPDATE => $allAttributes,
        ]);
    }


    public static function find()
    {
        $query = parent::find();

        if (static::hasSoftDelete()) {
            $isDeletedColumn = static::tableName() . '.' . static::$_isDeletedAttribute;
            $query->andOnCondition(['=', new Expression("COALESCE($isDeletedColumn, 0)"), 0]);
        }

        if (php_sapi_name() != "cli" && static::hasScopeCondition() && !Yii::$app->user->isGuest) {
            $scopeIds = [];
            $query->andOnCondition([static::tableName() . '.' . static::$_scopeAttribute => $scopeIds]);
        }

        return $query;
    }

    public static function findWithDeleted()
    {
        $query = parent::find();

        if (php_sapi_name() != "cli" && static::hasScopeCondition() && !Yii::$app->user->isGuest) {
            $scopeIds = [];
            $query->andOnCondition([static::tableName() . '.' . static::$_scopeAttribute => $scopeIds]);
        }

        return $query;
    }

    public static function hasSoftDelete()
    {
        $columnNames = static::getTableSchema()->getColumnNames();

        return in_array(static::$_isDeletedAttribute, $columnNames);
    }

    public static function hasScopeCondition()
    {
        $columnNames = static::getTableSchema()->getColumnNames();

        return in_array(static::$_scopeAttribute, $columnNames);
    }

    public function behaviors()
    {
        $behaviors = [];

        if (!is_a(Yii::$app, 'yii\console\Application')) {
            if ($this->hasAttribute(static::$_createdByAttribute) || $this->hasAttribute(static::$_updatedByAttribute)) {
                $behaviors[self::BLAMEABLE_BEHAVIOR_ID] = $this->getAttributeBehaviorConfig(BlameableBehavior::class, [
                    'insert' => ['createdByAttribute' => static::$_createdByAttribute],
                    'update' => ['updatedByAttribute' => static::$_updatedByAttribute]
                ]);
            }
        }

        if ($this->hasAttribute(static::$_createdAtAttribute) || $this->hasAttribute(static::$_updatedAtAttribute)) {
            $behaviors[self::TIMESTAMP_BEHAVIOR_ID] = $this->getAttributeBehaviorConfig(TimestampBehavior::class, [
                'insert' => ['createdAtAttribute' => static::$_createdAtAttribute],
                'update' => ['updatedAtAttribute' => static::$_updatedAtAttribute],
            ]);
        }

        return $behaviors;
    }

    protected function getAttributeBehaviorConfig($behaviorClass, $attributeMap)
    {
        $attributes = [];

        $insertBehaviorAttribute = key($attributeMap['insert']);
        $insertModelAttribute = $attributeMap['insert'][$insertBehaviorAttribute];

        $updateBehaviorAttribute = key($attributeMap['update']);
        $updateModelAttribute = $attributeMap['update'][$updateBehaviorAttribute];

        if ($this->hasAttribute($insertModelAttribute)) {
            $attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $insertModelAttribute;
        }

        if ($this->hasAttribute($updateModelAttribute)) {
            $attributes[BaseActiveRecord::EVENT_BEFORE_INSERT][] = $updateModelAttribute;
            $attributes[BaseActiveRecord::EVENT_BEFORE_UPDATE] = $updateModelAttribute;
        }

        return [
            'class' => $behaviorClass,
            $insertBehaviorAttribute => $insertModelAttribute,
            $updateBehaviorAttribute => $updateModelAttribute,
            'attributes' => $attributes
        ];
    }

    public function getAllAttributeNames()
    {
        return ArrayHelper::merge($this->getCurrentObjectProperties(), $this->attributes());
    }

    protected function getCurrentObjectProperties($filter = \ReflectionProperty::IS_PUBLIC)
    {
        $properties = [];
        $reflection = new \ReflectionClass($this);
        foreach ($reflection->getProperties($filter) as $property) {
            if ($property->class == $reflection->name) {
                $properties[] = $property->name;
            }
        }

        return $properties;
    }

    public static function createMultiple($modelClass, $multipleModels = [])
    {
        /** @var ActiveRecord $model */
        $model = new $modelClass;
        $formName = $model->formName();
        $post = Yii::$app->request->post($formName);
        $models = [];

        if (!empty($multipleModels)) {
            $keys = array_keys(ArrayHelper::map($multipleModels, 'id', 'id'));
            $multipleModels = array_combine($keys, $multipleModels);
        }

        if ($post && is_array($post)) {
            foreach ($post as $i => $item) {
                if (isset($item['id']) && !empty($item['id']) && isset($multipleModels[$item['id']])) {
                    $models[] = $multipleModels[$item['id']];
                } else {
                    $models[] = new $modelClass;
                }
            }
        }

        unset($model, $formName, $post);

        return $models;
    }

    /**
     * Saves multiple models.
     *
     * @param ActiveRecord[] $models
     * @return bool
     * @throws Exception
     */
    public static function insertMultiple($models)
    {
        if (empty($models)) {
            return false;
        }

        $firstModel = reset($models);
        $columnsToInsert = $firstModel->attributes();
        $rowsToInsert = [];

        foreach ($models as $model) {

            if (!($model->validate() && $model->beforeSave(true))) {
                throw new Exception('Model pre insert check failed.');
            }

            $rowsToInsert[] = array_values($model->attributes);
        }

        $numberAffectedRows = \Yii::$app->db->createCommand()
            ->batchInsert($firstModel->tableName(), $columnsToInsert, $rowsToInsert)
            ->execute();

        $isSuccess = ($numberAffectedRows === count($models));

        if ($isSuccess) {
            $changedAttributes = array_fill_keys($columnsToInsert, null);

            foreach ($models as $model) {
                $model->afterSave(true, $changedAttributes);
            }
        }

        return $isSuccess;
    }

    public static function loadMultiple($models, $data, $formName = null)
    {
        /** @var ActiveRecord[] $models */
        if ($formName === null) {
            $first = reset($models);
            if ($first === false) {
                return false;
            }
            $formName = $first->formName();
        }

        $success = false;

        foreach ($models as $i => $model) {
            if ($formName == '') {
                if (!empty($data[$i + 1])) {
                    $model->load($data[$i + 1], '');
                    $success = true;
                }
            } elseif (!empty($data[$formName][$i + 1])) {
                $model->load($data[$formName][$i + 1], '');
                $success = true;
            }
        }

        return $success;
    }

    public function isSoftDeleted()
    {
        return $this->{static::$_isDeletedAttribute} == 1;
    }

    public static function deleteAll($condition = '', $params = [])
    {
        if (static::hasSoftDelete()) {
            return static::updateAll([static::$_isDeletedAttribute => 1], $condition, $params);
        }

        return parent::deleteAll($condition, $params);
    }

    public static function permanentlyDeleteAll($condition = '', $params = [])
    {
        return parent::deleteAll($condition, $params);
    }

    public function afterDelete()
    {
        if (static::hasSoftDelete()) {
            $this->{static::$_isDeletedAttribute} = 1;
        }

        parent::afterDelete();
    }

    public function getPublicName()
    {
        return Inflector::titleize($this->getBaseName());
    }

    public function getBaseName()
    {
        return StringHelper::basename(get_class($this));
    }

    public function getETag()
    {
        return md5(get_class($this) . $this->getPrimaryKey() . $this->getLastModified());
    }

    public function getLastModified()
    {
        if ($this->hasAttribute(static::$_updatedAtAttribute) && $this->getAttribute(static::$_updatedAtAttribute)) {
            return $this->getAttribute(static::$_updatedAtAttribute);
        }

        if ($this->hasAttribute(static::$_createdAtAttribute) && $this->getAttribute(static::$_createdAtAttribute)) {
            return $this->getAttribute(static::$_createdAtAttribute);
        }

        return 0; //if no create or update time present, we consider resource last update time was 1970-01-01
    }

    public function getFormId()
    {
        return Inflector::slug($this->formName()) . '-id';
    }

    public static function getNextId()
    {
        return Yii::$app->db->createCommand('
                SELECT AUTO_INCREMENT 
                FROM INFORMATION_SCHEMA.TABLES 
                WHERE TABLE_SCHEMA = DATABASE() AND TABLE_NAME = :tableName', [':tableName' => static::tableName()]
        )->queryScalar();
    }

    public function delete()
    {
        if (!is_array(static::$_cascadeDeleteRelations) || empty(static::$_cascadeDeleteRelations)) {
            return parent::delete();
        }

        $transaction = \Yii::$app->db->beginTransaction();

        if (!parent::delete()) {
            $transaction->rollBack();
            return false;
        }

        if (!$this->cascadeDeleteRelations()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    public function cascadeDeleteRelations()
    {
        foreach (static::$_cascadeDeleteRelations as $relation) {
            if (!$this->hasProperty($relation)) {
                continue;
            }

            if (!is_array($this->{$relation})) {
                $model = $this->{$relation};
                if (empty($model)) {
                    continue;
                }

                if (!$model->delete()) {
                    return false;
                }
                continue;
            }

            foreach ($this->{$relation} as $model) {
                if (!$model->delete()) {
                    return false;
                }
            }
        }
        return true;
    }
}
