<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%board}}`.
 */
class m230110_103940_create_board_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%board}}', [
            'id' => $this->primaryKey(),
            'name' => $this->string(255),
            'slug' => $this->string(255),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'is_deleted' => $this->tinyInteger()->defaultValue(0)
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%board}}');
    }
}
