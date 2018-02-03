<?php

use yii\db\Migration;

/**
 * Class m170201_055640_notifications_table
 * Creating table for notifications
 */
class m170201_055640_notifications_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable('{{%notifications}}',[
            'id'                     => $this->primaryKey(),
            'title'                  => $this->string(120)->notNull(),
            'subject'                => $this->string(200),
            'text'                   => $this->text()->notNull(),
            'methods'                => $this->string(250)->notNull()->comment('json array with methods')->defaultValue('[]'),
            'receiver_class'         => $this->string(120)->notNull(),
            'receiver_field'         => $this->string(120)->comment('receiver field for selection'),
            'receiver_field_value'   => $this->string(250)->comment('receiver filed values json')->defaultValue('[]'),
            'created_at'             => $this->dateTime(),
            'updated_at'             => $this->dateTime()
        ]);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%notifications}}');
    }
}
