<?php

namespace app\modules\notifications\models\transports;

use Yii;
use app\modules\notifications\interfaces\AbstractNotificationTransport;
use app\modules\notifications\models\Notifications;
use yii\db\ActiveRecord;
use yii\swiftmailer\Message;

/**
 * Class EmailTransport
 * @package app\modules\notifications\models\transports
 */
class EmailTransport extends AbstractNotificationTransport
{
    /**
     * @var string DEFAULT_FROM Default sender email address
     */
    const DEFAULT_FROM = 'admin@admin.ru';

    /**
     * @var string $emailField Field of receiver's model comprising email address to send to
     */
    public $emailField = '';

    /**
     * @var string $fromEmail Field with sender email
     */
    public $fromEmail = '';

    /**
     * @inheritdoc
     */
    public function send(Notifications $notification, $sender = null, $receivers = [])
    {
        /* @var $item ActiveRecord */
        foreach($receivers as $item) {
            if($item->hasAttribute($this->emailField) || $item->hasProperty($this->emailField)) {
                $message = new Message();
                $message->setTo($item->{$this->emailField});
                $message->setFrom((! empty($this->fromEmail)) ? $this->fromEmail : self::DEFAULT_FROM);
                $message->setSubject($notification->compile('subject', $sender));
                $message->setHtmlBody($notification->compile('text', $sender));
                Yii::$app->mailer->send($message);
           }
        }
    }
}
