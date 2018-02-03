<?php

namespace app\modules\notifications\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "notifications".
 *
 * @property integer $id
 * @property string $title
 * @property string $subject
 * @property string $text
 * @property string $methods                Notification transports
 * @property string $receiver_class         Model class of notification receiver
 * @property string $receiver_field         Field of $receiver_class to be used in where() for selecting receivers
 * @property string $receiver_field_value   Values of $receiver_class to be used in in where() for selecting receivers
 * @property string $created_at
 * @property string $updated_at
 */
class Notifications extends ActiveRecord
{
    /**
     * @var array $owner_ids Array for owner modes ids, used for save/update purpose
     */
    public $owner_ids = [];

    /**
     * @var array $methods_array Array representation of $methods JSON attribute
     */
    public $methods_array = [];

    /**
     * @var array $receiver_field_value_array Array representation of $receiver_field_value JSON attribute
     */
    public $receiver_field_value_array = [];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'notifications';
    }

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge(parent::behaviors(), [
            'timestamp' => [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()')
            ]
        ]);
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'text', 'receiver_class'], 'required'],
            [['subject', 'text', 'receiver_field', 'receiver_field_value'], 'string'],
            [['owner_ids'], 'each', 'rule' => ['integer']],
            [['methods_array'], 'each', 'rule' => ['string']],
            [['title'], 'string', 'max' => 120],
            [['methods'], 'string'],
            [['created_at', 'updated_at', 'receiver_field_value_array'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'         => 'ID',
            'title'      => 'Title',
            'text'       => 'Text',
            'methods'    => 'Methods',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
        ];
    }

    /**
     * Common method to forward request to transport item
     *
     * @param ActiveRecord $model Notification sender model
     */
    public function send($model = null)
    {
        $transports = Yii::$app->getModule('notifications')->getTransports();
        foreach ($this->methods_array as $method) {
            if (array_key_exists($method, $transports)) {
                $transports[$method]->send($this, $model, $this->getReceiversList($method, $model));
            }
        }
    }

    /**
     * Compiling template field into text message using ActiveRecord model
     *
     * @param string $field
     * @param ActiveRecord $model
     * @return string
     */
    public function compile($field, $model = null)
    {

        if ($this->hasProperty($field) || $this->hasAttribute($field)) {
            $field_value = $this->{$field};
            if (null == $model) {
                return $field_value;
            }
            $replace_array = [];
            preg_match_all('/\{\w+\}/', $field_value, $matches);

            if (! isset($matches[0]) || ! is_array($matches[0])) {
                return $field_value;
            }
            foreach ($matches[0] as $item) {
                $attribute = str_replace(['{', '}'], '', $item);
                if ($model->hasAttribute($attribute) || $model->hasProperty($attribute)) {
                    $replace_array[$item] = $model->{$attribute};
                }
            }

            return strtr($field_value, $replace_array);
        }

        return '';
    }

    /**
     * Getting list of notification receivers
     *
     * @param string       $method Code name of transport
     * @param ActiveRecord $sender Notification sender model
     *
     * @return array
     */
    public function getReceiversList($method, $sender)
    {
        $receivers = [];
        if (! empty($this->receiver_field_value_array) && ! empty($this->receiver_field)) {
            $class = $this->receiver_class;
            $receivers = $class::find()->where([$this->receiver_field => $this->receiver_field_value_array])->all();
        } elseif(null !== $sender) {
            $receivers[] = $sender;
        }
        //checking for profile setting, approved method should be in notificationSettingsField array in receiver model
        $settings_field = Yii::$app->getModule('notifications')->notificationSettingsField;
        if (! empty($settings_field)) {
            foreach ($receivers as $index => $receiver) {
                if ($receiver->hasProperty($settings_field) && is_array($receiver->{$settings_field})) {
                    if (! in_array($method, $receiver->{$settings_field})) {
                        unset($receivers[$index]);
                    }
                }
            }
        }

        return $receivers;
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        $this->methods              = json_encode($this->methods_array);
        $this->receiver_field_value = json_encode($this->receiver_field_value_array);

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $this->processOwnerLinks();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        $this->methods_array              = json_decode($this->methods, true);
        $this->receiver_field_value_array = json_decode($this->receiver_field_value, true);
        $this->owner_ids                  = OwnerNotification::find()
            ->select('owner_id')
            ->where(['notification_id' => $this->id])
            ->column();

        parent::afterFind();
    }

    /**
     * Processing of links between notification and owner model
     *
     * @return void
     */
    public function processOwnerLinks()
    {
        if (! empty($this->owner_ids)) {
            OwnerNotification::deleteAll(['notification_id' => $this->id]);
            foreach ($this->owner_ids as $owner_id) {
                $link = new OwnerNotification([
                    'owner_id'        => $owner_id,
                    'notification_id' => $this->id
                ]);
                $link->save();
            }
        }
    }
}
