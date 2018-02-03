<?php

namespace app\modules\notifications\interfaces;

use app\modules\notifications\models\Notifications;
use yii\base\Component;
use yii\db\ActiveRecord;

/**
 * Class AbstractNotificationTransport
 */
abstract class AbstractNotificationTransport extends Component
{
    /**
     * @var string $code_name Code name of transport used in list
     */
    protected $codeName = '';

    /**
     * Getter for code name
     * @return string
     */
    public function getCodeName()
    {
        return $this->codeName;
    }

    /**
     * Getter for code name
     *
     * @param string $value
     * @return void
     */
    public function setCodeName($value)
    {

        if ('' === $this->codeName) {
            $this->codeName = $value;
        }
    }

    /**
     * Interface function to handle notification sending
     *
     * @param Notifications $notification
     * @param ActiveRecord|null $sender     Model using in template compilation, real sender of notification
     * @param array $receivers              Array of objects-receivers of notification
     * @return mixed
     */
    abstract public function send(Notifications $notification, $sender = null, $receivers = []);
}
