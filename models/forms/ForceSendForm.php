<?php

namespace app\modules\notifications\models\forms;

use Yii;
use yii\base\Model;

/**
 * ForceSendForm form
 */
class ForceSendForm extends Model
{

    /**
     * @var string $receiver_field
     */
    public $receiver_field;

    /**
     * @var array $receiver_field_value
     */
    public $receiver_field_values = [];

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['receiver_field', 'receiver_field_values'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'receiver_field'        => 'Receiver field ',
            'receiver_field_values' => 'Receiver field values',
        ];
    }

    /**
     * Force send notification

     * @param \app\modules\notifications\models\Notifications $notification
     * @return null
     */
    public function send($notification)
    {
        $notification->receiver_field             = $this->receiver_field;
        $notification->receiver_field_value_array = $this->receiver_field_values;
        $notification->send();
        return true;
    }
}
