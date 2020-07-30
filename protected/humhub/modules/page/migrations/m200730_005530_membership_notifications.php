<?php

use yii\db\Migration;

class m200730_005530_membership_notifications extends Migration
{
    public function up()
    {
        $this->addColumn('page_membership', 'send_notifications', 'boolean default "0"');
    }

    public function down()
    {
        echo "m170110_151419_membership_notifications cannot be reverted.\n";

        return false;
    }

    /*
    // Use safeUp/safeDown to run migration code within a transaction
    public function safeUp()
    {
    }

    public function safeDown()
    {
    }
    */
}
