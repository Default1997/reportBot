<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "bot_user".
 *
 * @property int $id
 * @property string|null $username
 * @property int|null $telegram_id
 * @property int|null $department_id
 */
class BotUser extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'bot_user';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['telegram_id', 'department_id'], 'integer'],
            [['username'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'username' => 'Username',
            'telegram_id' => 'Telegram ID',
            'department_id' => 'Department ID',
        ];
    }
}
