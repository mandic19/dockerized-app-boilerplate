<?php

namespace api\models\forms;

use app\models\Section;
use api\helpers\ReorderHelper;
use app\models\Task;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

class TaskReorderForm extends Task
{
    private $oldOrder;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['order'], 'required']
        ]);
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        $transaction = \Yii::$app->db->beginTransaction();

        $this->oldOrder = $this->getOldAttribute('order');
        if (!parent::save($runValidation, $attributeNames)) {
            $transaction->rollBack();
            return false;
        }

        if (!$this->updateOrderHierarchy()) {
            $transaction->rollBack();
            return false;
        }

        $transaction->commit();
        return true;
    }

    protected function updateOrderHierarchy()
    {
        list($inc, $bottomLimit, $topLimit) = ReorderHelper::getParameters($this->oldOrder, $this->order);

        Task::updateAll(
            ['order' => new Expression("IFNULL(`order`, 0) + {$inc}")],
            [
                'AND',
                ['BETWEEN', new Expression('IFNULL(`order`, 0)'), $bottomLimit, $topLimit],
                ['!=', 'id', $this->id]
            ]
        );
        return true;
    }
}
