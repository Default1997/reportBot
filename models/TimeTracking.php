<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "time_tracking".
 *
 * @property int $id
 * @property int|null $user_id
 * @property string|null $start
 * @property string|null $end
 * @property string|null $break_start
 * @property string|null $break_end
 */
class TimeTracking extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'time_tracking';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'integer'],
            [['start', 'end', 'break_start', 'break_end'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'start' => 'Start',
            'end' => 'End',
            'break_start' => 'Break Start',
            'break_end' => 'Break End',
        ];
    }

    public function start($timeTracking, $telegramUserId)
    {
        $timeTracking->user_id = $telegramUserId;
        $timeTracking->start = date_create('today')->getTimestamp();
        $timeTracking->save();
    }
}
