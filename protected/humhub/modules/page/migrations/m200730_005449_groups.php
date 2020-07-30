<?php

use yii\db\Schema;
use yii\db\Migration;

class m200730_005449_groups extends Migration
{

    public function up()
    {
        $this->addColumn('page_membership', 'group_id', Schema::TYPE_STRING. " DEFAULT 'member'");
        $this->update('page_membership', ['group_id' => 'admin'], 'page_membership.admin_role=1');

        $this->dropColumn('page_membership', 'admin_role');
        $this->dropColumn('page_membership', 'share_role');
        $this->dropColumn('page_membership', 'invite_role');
    }

    public function down()
    {
        echo "m150928_134934_groups cannot be reverted.\n";

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
