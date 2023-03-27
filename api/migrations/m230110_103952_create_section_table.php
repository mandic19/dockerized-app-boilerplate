<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%list}}`.
 */
class m230110_103952_create_section_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%section}}', [
            'id' => $this->primaryKey(),
            'board_id' => $this->integer()->notNull(),
            'name' => $this->string(255),
            'order' => $this->integer(),
            'created_at' => $this->integer(),
            'created_by' => $this->integer(),
            'updated_at' => $this->integer(),
            'updated_by' => $this->integer(),
            'is_deleted' => $this->tinyInteger()->defaultValue(0)
        ], 'CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE=InnoDB');

        $this->addForeignKey('fk_board_section', 'section', 'board_id', 'board', 'id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk_board_list', 'list');

        $this->dropTable('{{%list}}');
    }
}
