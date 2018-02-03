<?php

namespace app\modules\notifications\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "owner_notification".
 *
 * @property integer $id
 * @property integer $event_id
 * @property integer $notification_id
 */
class OwnerNotification extends ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'owner_notification';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['owner_id', 'notification_id'], 'required'],
            [['owner_id', 'notification_id'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => 'ID',
            'owner_id'        => 'Event ID',
            'notification_id' => 'Notification ID',
        ];
    }
}
