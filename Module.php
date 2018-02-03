<?php

namespace app\modules\notifications;

use app\modules\notifications\interfaces\AbstractNotificationTransport;
use Yii;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;

/**
 * Class Module
 * @package app\modules\notifications
 */
class Module extends \yii\base\Module
{
    /**
     * @inheritdoc
     */
    public $controllerNamespace = 'app\modules\notifications\controllers';

    /**
     * @inheritdoc
     */
    public $defaultRoute = 'notifications/index';

    /**
     * @var string $ownerClass class name with namespace of model notification should be linked to
     */
    public $ownerClass;

    /**
     * @var string owner model field with title of item, used to make list of owner's models
     */
    public $ownerTitleField;

    /**
     * @var integer owner model primary key, used to make list of owner's models
     */
    public $ownerPkField;

    /**
     * @var string $receiverClass class name with namespace of model which items should be receivers of notification
     */
    public $receiverClass;

    /**
     * @var string $receiverFields array receiver class model fields, should include 2 keys: fieldValuesMethod and fieldName
     */
    public $receiverFields;

    /**
     * @var string $notificationSettingsField
     */
    public $notificationSettingsField;

    /**
     * @var array
     */
    public $transports = [];

    /**
     * @var array
     */
    protected $_transports = [];

    /**
     * @var string
     */
    protected $owner;

    /**
     * @var array
     */
    protected $_transportList = [];

    /**
     * @inheritdoc
     */
    public function init()
    {
        if (! empty($this->ownerClass)) {
            $this->owner = new $this->ownerClass;
            if (! ($this->owner instanceof ActiveRecord) || is_array($this->owner->getPrimaryKey())) {
                throw new InvalidConfigException('OwnerClass should be instance of ActiveRecord and Pk should not be a array');
            }
            if(empty($this->ownerPkField) && (! $this->owner->hasProperty($this->ownerPkField) || ! $this->owner->hasAttribute($this->ownerPkField))){
                throw new InvalidConfigException('OwnerPkField should be set and should be property of owner class');
            }

            if(empty($this->ownerTitleField) && (! $this->owner->hasProperty($this->ownerTitleField) || ! $this->owner->hasAttribute($this->ownerTitleField))){
                throw new InvalidConfigException('OwnerTitleField should be set and should be property of owner class');
            }
        }

        if (! empty($this->transports)) {
            foreach ($this->transports as $name => $config) {
                if (is_array($config) && ! isset($config['class'])) {
                    throw new InvalidConfigException('Transport array config should consists class key');
                } elseif (is_string($config) || is_array($config)) {
                    $this->_transports[is_array($config) ? $name : $config] = Yii::createObject(array_merge($config,
                        ['codeName' => is_array($config) ? $name : $config]));
                } else {
                    throw new InvalidConfigException('Handler param set incorrect');
                }
            }
        } else {
            throw new InvalidConfigException('You should set at least one transport');
        }

        foreach ($this->_transports as $item) {
            if (! ($item instanceof AbstractNotificationTransport)) {
                throw new InvalidConfigException('Transport should be extended from AbstractNotificationTransport');
            }
        }

        $this->_transportList = array_combine(array_keys($this->_transports), array_keys($this->_transports));

        parent::init();
    }

    /**
     * @return array
     */
    public function getTransports()
    {
        return $this->_transports;
    }

    /**
     * @return array
     */
    public function getTransportList()
    {
        return $this->_transportList;
    }
}
