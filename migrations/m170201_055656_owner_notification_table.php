<?php

use yii\db\Migration;

/**
 * Class m170201_055656_event_notification_table
 * Creating table links between notification and
 */
class m170201_055656_owner_notification_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%owner_notification}}',[
            'id'                     => $this->primaryKey(),
            'owner_id'               => $this->integer()->notNull(),
            'notification_id'        => $this->integer()->notNull(),
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%owner_notification}}');
    }
}
