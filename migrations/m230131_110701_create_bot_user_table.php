<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%bot_user}}`.
 */
class m230131_110701_create_bot_user_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%bot_user}}', [
            'id' => $this->primaryKey(),
            'username' => $this->string(),
            'telegram_id' => $this->integer(),
            'department_id' => $this->integer(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%bot_user}}');
    }
}
