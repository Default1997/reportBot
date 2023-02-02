<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%time_tracking}}`.
 */
class m230131_110248_create_time_tracking_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%time_tracking}}', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'start' => $this->timestamp(),
            'end' => $this->timestamp(),
            'break_start' => $this->timestamp(),
            'break_end' => $this->timestamp(),
            'break_sum' => $this->float(),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%time_tracking}}');
    }
}
