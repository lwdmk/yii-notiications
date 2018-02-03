<?php

namespace app\modules\notifications\models\transports;

use Yii;
use app\modules\notifications\interfaces\AbstractNotificationTransport;
use app\modules\notifications\models\Notifications;
use yii\db\ActiveRecord;

/**
 * Class BrowserTransport
 */
class BrowserTransport extends AbstractNotificationTransport
{
    /**
     * @inheritdoc
     */
    public function send(Notifications $notification, $sender = null, $receivers = [])
    {
        /* @var $item ActiveRecord */
        foreach($receivers as $item) {
            Yii::$app->session->setFlash('notification_for_'.$item->getPrimaryKey(), $notification->compile('text', $sender));
        }
    }
}
