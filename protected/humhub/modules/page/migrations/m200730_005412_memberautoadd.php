<?php


use yii\db\Migration;

class m200730_005412_memberautoadd extends Migration {

    public function up() {
        $this->addColumn('page', 'auto_add_new_members', 'int(4) DEFAULT NULL');
    }

    public function down() {
        echo "m140304_142711_memberautoadd does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
